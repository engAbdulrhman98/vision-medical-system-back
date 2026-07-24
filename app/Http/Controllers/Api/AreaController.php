<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Area;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    public function index(Request $request)
    {
        $query = Area::with('parent');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        $areas = $query->get();

        return response()->json($areas);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'type' => 'required|in:governorate,borough,city,town,village,hamlet',
            'parent_id' => 'nullable|exists:areas,id',
        ]);

        $area = Area::create($validated);

        return response()->json($area, 201);
    }

    public function show(Area $area)
    {
        return response()->json($area->load('parent'));
    }

    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required',
            'type' => 'sometimes|required|in:governorate,borough,city,town,village,hamlet',
            'parent_id' => 'nullable|exists:areas,id',
        ]);

        $area->update($validated);

        return response()->json($area);
    }

    public function destroy(Area $area)
    {
        $area->delete();

        return response()->json(['message' => 'Area deleted successfully']);
    }

    public function export()
    {
        return response()->json(Area::all());
    }

    public function import(Request $request)
    {
        return response()->json(['message' => 'Imported successfully']);
    }
}
