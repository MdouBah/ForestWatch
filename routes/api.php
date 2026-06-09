<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ZoneForestierController;
use App\Http\Controllers\Api\EspeceArbreController;
use App\Http\Controllers\Api\AnalyseController;
use App\Http\Controllers\Api\CoursEauController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\RapportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — FORESTWATCH
|--------------------------------------------------------------------------
|
| Middleware auth:api → JWT requis pour toutes les routes protégées.
|
| Rôles :
|   admin       → accès total
|   user (agent)→ lecture + écriture zones/analyses/rapports, pas gestion users
|   visiteur    → lecture seule (zones, espèces, cours d'eaux, carte)
|
*/

/* ─── AUTH PUBLIQUE ─────────────────────────────────────────────── */
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login',    [AuthController::class, 'login']);
});

/* ─── ROUTES PROTÉGÉES (JWT requis) ────────────────────────────── */
Route::middleware('auth:api')->group(function () {

    // ── Auth ──────────────────────────────────────────────────────
    Route::prefix('auth')->group(function () {
        Route::get('/me',             [AuthController::class, 'me']);
        Route::post('/logout',        [AuthController::class, 'logout']);
        Route::put('/profile',        [AuthController::class, 'updateProfile']);
        Route::post('/profile/photo',        [AuthController::class, 'uploadPhoto']);
        Route::post('/profile/photo/delete', [AuthController::class, 'deletePhoto']);
    });

    // ── Zones forestières — LECTURE pour tous ─────────────────────
    Route::get('/zones/statistiques', [ZoneForestierController::class, 'statistiques']);
    Route::get('/zones',              [ZoneForestierController::class, 'index']);
    Route::get('/zones/{zone}',       [ZoneForestierController::class, 'show']);

    // ── Espèces d'arbres — LECTURE pour tous ─────────────────────
    Route::get('/especes',          [EspeceArbreController::class, 'index']);
    Route::get('/especes/{espece}', [EspeceArbreController::class, 'show']);

    // ── Cours d'eaux — LECTURE pour tous ──────────────────────────
    Route::get('/cours-eaux',      [CoursEauController::class, 'index']);
    Route::get('/cours-eaux/{id}', [CoursEauController::class, 'show']);

    // ── Dashboard stats — accessibles à tous ──────────────────────
    Route::get('/analyses/dashboard',   [AnalyseController::class, 'dashboard']);

    // ── Routes AGENTS + ADMINS (not_visiteur) ─────────────────────
    Route::middleware('not_visiteur')->group(function () {

        // Zones — ÉCRITURE pour agents et admins
        Route::post('/zones',          [ZoneForestierController::class, 'store']);
        Route::put('/zones/{zone}',    [ZoneForestierController::class, 'update']);
        Route::delete('/zones/{zone}', [ZoneForestierController::class, 'destroy']);

        // Analyses — CRUD complet
        Route::apiResource('/analyses', AnalyseController::class)->except(['index', 'show']);
        Route::get('/analyses',         [AnalyseController::class, 'index']);
        Route::get('/analyses/{id}',    [AnalyseController::class, 'show']);

        // Rapports — liste, génération, suppression
        // NOTE: Le téléchargement se fait via l'URL publique directe :
        //   http://localhost:8000/storage/rapports/fichier.pdf
        // (retournée dans download_url — pas d'auth JWT requise pour les fichiers publics)
        Route::get('/rapports',                         [RapportController::class, 'index']);
        Route::get('/rapports/statistiques',            [RapportController::class, 'statistiques']);
        Route::get('/rapports/{id}',                    [RapportController::class, 'show']);
        Route::post('/rapports/generer/{analyseId}',    [RapportController::class, 'generer']);
        Route::delete('/rapports/{id}',                 [RapportController::class, 'destroy']);
    });

    // ── Routes ADMIN UNIQUEMENT ───────────────────────────────────
    Route::middleware('admin')->group(function () {

        // Espèces — ÉCRITURE admin seulement
        Route::post('/especes',            [EspeceArbreController::class, 'store']);
        Route::put('/especes/{espece}',    [EspeceArbreController::class, 'update']);
        Route::delete('/especes/{espece}', [EspeceArbreController::class, 'destroy']);

        // Cours d'eaux — ÉCRITURE admin seulement
        Route::post('/cours-eaux',         [CoursEauController::class, 'store']);
        Route::put('/cours-eaux/{id}',     [CoursEauController::class, 'update']);
        Route::delete('/cours-eaux/{id}',  [CoursEauController::class, 'destroy']);

        // Gestion des utilisateurs / agents
        Route::prefix('admin')->group(function () {
            Route::get('/users',               [UserController::class, 'index']);
            Route::post('/users',              [UserController::class, 'store']);
            Route::put('/users/{id}/role',     [UserController::class, 'updateRole']);
            Route::delete('/users/{id}',       [UserController::class, 'destroy']);
            // Mise à jour complète d'un utilisateur (nom, email, etc.)
            Route::put('/users/{id}',          [UserController::class, 'update']);
        });
    });
});
