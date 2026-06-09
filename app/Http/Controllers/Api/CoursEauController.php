<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CoursEau;
use Illuminate\Http\Request;

class CoursEauController extends Controller
{
    public function index()
    {
        return response()->json(CoursEau::with('zones')->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'         => 'required|string|max:150',
            'type'        => 'required|in:rivière,fleuve,lac',
            'longueur'    => 'nullable|numeric|min:0',
            'debit'       => 'nullable|numeric|min:0',
            'coordonnees' => 'nullable|array',
        ]);

        return response()->json(CoursEau::create($data), 201);
    }

    public function show(string $id)
    {
        $cours = CoursEau::with('zones')->findOrFail($id);
        return response()->json($cours);
    }

    public function update(Request $request, string $id)
    {
        $cours = CoursEau::findOrFail($id);

        $data = $request->validate([
            'nom'         => 'sometimes|string|max:150',
            'type'        => 'sometimes|in:rivière,fleuve,lac',
            'longueur'    => 'nullable|numeric|min:0',
            'debit'       => 'nullable|numeric|min:0',
            'coordonnees' => 'nullable|array',
        ]);

        $cours->update($data);
        return response()->json($cours);
    }

    public function destroy(string $id)
    {
        CoursEau::findOrFail($id)->delete();
        return response()->json(['message' => 'Cours d\'eau supprimé']);
    }
}
