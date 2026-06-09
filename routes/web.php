<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — FORESTWATCH
|--------------------------------------------------------------------------
|
| Toutes les vues sont des SPA légères : elles s'appuient sur le JWT stocké
| en localStorage. L'authentification et les autorisations sont gérées :
|   - côté serveur par les middlewares JWT (api.php)
|   - côté client par le JavaScript du layout (layouts/app.blade.php)
|
*/

// ── Page d'accueil → redirection vers login ─────────────────────────────────
Route::get('/', fn() => redirect('/login'));

// ── Authentification (publiques) ─────────────────────────────────────────────
Route::get('/login',    fn() => view('auth.login'))->name('login');
Route::get('/register', fn() => view('auth.register'))->name('register');

// ── Pages utilisateur (accessibles à tous les rôles connectés) ──────────────
Route::get('/dashboard',  fn() => view('dashboard.index'));
Route::get('/carte',      fn() => view('carte.index'));
Route::get('/zones',      fn() => view('zones.index'));
Route::get('/especes',    fn() => view('especes.index'));
Route::get('/cours-eaux', fn() => view('cours-eaux.index'));
Route::get('/profil',     fn() => view('profil.index'));

// ── Pages réservées aux agents et admins (vérification JS) ──────────────────
Route::get('/analyses',   fn() => view('analyses.index'));
Route::get('/rapports',   fn() => view('rapports.index'));

// ── Pages réservées à l'administrateur (vérification JS + API) ──────────────
Route::prefix('admin')->group(function () {
    Route::get('/utilisateurs', fn() => view('admin.utilisateurs'));
});
