<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Analyse;
use Illuminate\Http\Request;

class AnalyseController extends Controller
{
    public function index(Request $request)
    {
        $query = Analyse::with(['zone', 'user'])->latest('date_analyse');

        if ($request->filled('zone_id')) {
            $query->where('zone_forestiere_id', $request->zone_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'zone_forestiere_id'  => 'required|exists:zones_forestieres,id',
            'type_analyse'        => 'required|string|max:100',
            'resultat'            => 'nullable|string',
            'superficie_concernee'=> 'nullable|numeric|min:0',
            'taux_deforestation'  => 'nullable|numeric|min:0|max:100',
            'observations'        => 'nullable|string',
        ]);

        $analyse = Analyse::create(array_merge($data, [
            'user_id'      => auth('api')->id(),
            'date_analyse' => now(),
        ]));

        return response()->json($analyse->load(['zone', 'user']), 201);
    }

    public function show(string $id)
    {
        $analyse = Analyse::with(['zone', 'user', 'rapports'])->findOrFail($id);
        return response()->json($analyse);
    }

    public function update(Request $request, string $id)
    {
        $analyse = Analyse::findOrFail($id);

        $data = $request->validate([
            'type_analyse'        => 'sometimes|string|max:100',
            'resultat'            => 'nullable|string',
            'superficie_concernee'=> 'nullable|numeric|min:0',
            'taux_deforestation'  => 'nullable|numeric|min:0|max:100',
            'observations'        => 'nullable|string',
        ]);

        $analyse->update($data);
        return response()->json($analyse);
    }

    public function destroy(string $id)
    {
        Analyse::findOrFail($id)->delete();
        return response()->json(['message' => 'Analyse supprimée']);
    }

    public function dashboard()
    {
        return response()->json([
            'total_analyses'           => Analyse::count(),
            'taux_moyen_deforestation' => round(Analyse::whereNotNull('taux_deforestation')->avg('taux_deforestation'), 2),
            'recentes'                 => Analyse::with('zone')->latest('date_analyse')->take(5)->get(),
            'par_type'                 => Analyse::selectRaw('type_analyse, COUNT(*) as total')->groupBy('type_analyse')->get(),
        ]);
    }
}
