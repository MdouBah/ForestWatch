<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ZoneForestiere;
use Illuminate\Http\Request;

class ZoneForestierController extends Controller
{
    public function index(Request $request)
    {
        $query = ZoneForestiere::with(['especes', 'coursEaux', 'latestAnalyse'])
                               ->withCount('analyses');

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }
        if ($request->filled('etat')) {
            $query->where('etat', $request->etat);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nom'        => 'required|string|max:150',
            'superficie' => 'required|numeric|min:0',
            'latitude'   => 'required|numeric|between:-90,90',
            'longitude'  => 'required|numeric|between:-180,180',
            'region'     => 'required|string|max:100',
            'etat'       => 'required|in:sain,dégradé,critique',
        ]);

        $zone = ZoneForestiere::create(array_merge($data, ['user_id' => auth('api')->id()]));

        return response()->json($zone, 201);
    }

    public function show(string $id)
    {
        $zone = ZoneForestiere::with(['especes', 'coursEaux', 'analyses.user'])->findOrFail($id);
        return response()->json($zone);
    }

    public function update(Request $request, string $id)
    {
        $zone = ZoneForestiere::findOrFail($id);

        $data = $request->validate([
            'nom'        => 'sometimes|string|max:150',
            'superficie' => 'sometimes|numeric|min:0',
            'latitude'   => 'sometimes|numeric|between:-90,90',
            'longitude'  => 'sometimes|numeric|between:-180,180',
            'region'     => 'sometimes|string|max:100',
            'etat'       => 'sometimes|in:sain,dégradé,critique',
        ]);

        $zone->update($data);
        return response()->json($zone);
    }

    public function destroy(string $id)
    {
        ZoneForestiere::findOrFail($id)->delete();
        return response()->json(['message' => 'Zone supprimée']);
    }

    public function statistiques()
    {
        return response()->json([
            'total_zones'    => ZoneForestiere::count(),
            'par_etat'       => ZoneForestiere::selectRaw('etat, COUNT(*) as total, SUM(superficie) as superficie_totale')
                                    ->groupBy('etat')->get(),
            'par_region'     => ZoneForestiere::selectRaw('region, COUNT(*) as total, SUM(superficie) as superficie_totale')
                                    ->groupBy('region')->get(),
            'superficie_totale' => ZoneForestiere::sum('superficie'),
        ]);
    }
}
