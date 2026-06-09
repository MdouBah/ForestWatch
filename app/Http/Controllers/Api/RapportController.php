<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Analyse;
use App\Models\Rapport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * RapportController
 *
 * Gère la création, la liste et la suppression des rapports PDF.
 * La génération utilise barryvdh/laravel-dompdf.
 *
 * Routes associées :
 *   GET    /api/rapports               → index()
 *   POST   /api/rapports/generer/{id}  → generer()  (génère le PDF à partir d'une analyse)
 *   GET    /api/rapports/{id}          → show()
 *   DELETE /api/rapports/{id}          → destroy()
 */
class RapportController extends Controller
{
    /** ── Liste de tous les rapports de l'utilisateur (ou tous pour l'admin) ── */
    public function index()
    {
        $user = auth('api')->user();

        $query = Rapport::with(['analyse.zone', 'user'])
                        ->latest();

        // L'admin voit tous les rapports ; l'agent ne voit que les siens
        if ($user->role !== 'admin') {
            $query->where('user_id', $user->id);
        }

        return response()->json($query->get());
    }

    /** ── Détail d'un rapport ── */
    public function show(string $id)
    {
        $rapport = Rapport::with(['analyse.zone', 'user'])->findOrFail($id);

        $user = auth('api')->user();
        if ($user->role !== 'admin' && $rapport->user_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        return response()->json($rapport);
    }

    /**
     * ── Générer un rapport PDF à partir d'une analyse ──
     *
     * 1. Charge l'analyse avec ses relations
     * 2. Génère le PDF via dompdf (template pdf.rapport)
     * 3. Stocke le fichier dans storage/app/public/rapports/
     * 4. Enregistre le rapport en base de données
     * 5. Retourne l'URL de téléchargement
     */
    public function generer(string $analyseId)
    {
        $analyse = Analyse::with(['zone.especes', 'zone.coursEaux', 'user'])->findOrFail($analyseId);
        $user    = auth('api')->user();

        // Vérification : l'utilisateur doit être agent ou admin
        if (! in_array($user->role, ['admin', 'user'])) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // Titre du rapport
        $titre = 'Rapport – ' . $analyse->zone->nom . ' – ' . $analyse->type_analyse
               . ' (' . now()->format('d/m/Y') . ')';

        // Génération du PDF via dompdf
        $pdf = Pdf::loadView('pdf.rapport', [
            'analyse' => $analyse,
            'zone'    => $analyse->zone,
            'user'    => $user,
            'titre'   => $titre,
            'date'    => now()->format('d/m/Y à H:i'),
        ])->setPaper('A4');

        // Nom de fichier unique
        $filename  = 'rapport_' . Str::slug($analyse->zone->nom) . '_' . now()->format('Ymd_His') . '.pdf';
        $directory = 'rapports';
        $path      = $directory . '/' . $filename;

        // Stockage dans storage/app/public/rapports/
        Storage::disk('public')->put($path, $pdf->output());

        // Enregistrement en base
        $rapport = Rapport::create([
            'analyse_id'  => $analyse->id,
            'user_id'     => $user->id,
            'titre'       => $titre,
            'format'      => 'PDF',
            'contenu'     => null,
            'url_fichier' => $path,   // chemin relatif dans le disk 'public'
        ]);

        // Charger les relations pour que download_url soit calculé avec les bonnes données
        $rapport->load(['analyse.zone', 'user']);

        return response()->json([
            'message'      => 'Rapport PDF généré avec succès.',
            'rapport'      => $rapport,
            // download_url est l'URL publique directe (pas d'auth JWT requise)
            // ex: http://localhost:8000/storage/rapports/rapport_xxx.pdf
            'download_url' => $rapport->download_url,
        ], 201);
    }

    /** ── Supprimer un rapport ── */
    public function destroy(string $id)
    {
        $rapport = Rapport::findOrFail($id);
        $user    = auth('api')->user();

        if ($user->role !== 'admin' && $rapport->user_id !== $user->id) {
            return response()->json(['message' => 'Accès refusé.'], 403);
        }

        // Supprimer le fichier du disque
        if ($rapport->url_fichier && Storage::disk('public')->exists($rapport->url_fichier)) {
            Storage::disk('public')->delete($rapport->url_fichier);
        }

        $rapport->delete();

        return response()->json(['message' => 'Rapport supprimé.']);
    }

    /** ── Statistiques des rapports (pour le dashboard admin) ── */
    public function statistiques()
    {
        $user = auth('api')->user();

        $query = $user->role === 'admin'
            ? Rapport::query()
            : Rapport::where('user_id', $user->id);

        return response()->json([
            'total_rapports' => $query->count(),
            'par_format'     => (clone $query)
                ->selectRaw('format, COUNT(*) as total')
                ->groupBy('format')
                ->get(),
        ]);
    }
}
