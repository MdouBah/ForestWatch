<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\EspeceArbre;
use Illuminate\Http\Request;

class EspeceArbreController extends Controller
{
    public function index(Request $request)
    {
        $query = EspeceArbre::query();

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }
        if ($request->filled('famille')) {
            $query->where('famille', $request->famille);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom_commun'       => 'required|string|max:150',
            'nom_scientifique' => 'required|string|max:200|unique:especes_arbres',
            'famille'          => 'required|string|max:100',
            'description'      => 'nullable|string',
            'image_url'        => 'nullable|url',
            'statut'           => 'required|in:commun,rare,menacé',
        ]);

        return response()->json(EspeceArbre::create($data), 201);
    }

    public function show(string $id)
    {
        $espece = EspeceArbre::with('zones')->findOrFail($id);
        return response()->json($espece);
    }

    public function update(Request $request, string $id)
    {
        $espece = EspeceArbre::findOrFail($id);

        $data = $request->validate([
            'nom_commun'       => 'sometimes|string|max:150',
            'nom_scientifique' => 'sometimes|string|max:200|unique:especes_arbres,nom_scientifique,' . $id,
            'famille'          => 'sometimes|string|max:100',
            'description'      => 'nullable|string',
            'image_url'        => 'nullable|url',
            'statut'           => 'sometimes|in:commun,rare,menacé',
        ]);

        $espece->update($data);
        return response()->json($espece);
    }

    public function destroy(string $id)
    {
        EspeceArbre::findOrFail($id)->delete();
        return response()->json(['message' => 'Espèce supprimée']);
    }
}
