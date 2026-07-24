<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::with(['area', 'contacts']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
        }

        $clients = $query->latest()->get();

        return response()->json($clients);
    }

    public function store(Request $request)
    {
        if ($request->has('area_id') && (int)$request->area_id <= 0) {
            $request->merge(['area_id' => null]);
        }

        $validated = $request->validate([
            'name' => 'required',
            'type' => 'nullable|string',
            'area_id' => 'nullable|exists:areas,id',
            'governorate' => 'nullable|string',
            'city' => 'nullable|string',
            'detailed_address' => 'nullable|string',
            'phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $client = Client::create($validated);

        return response()->json($client, 201);
    }

    public function show(Client $client)
    {
        return response()->json($client->load(['area', 'contacts']));
    }

    public function update(Request $request, Client $client)
    {
        if ($request->has('area_id') && (int)$request->area_id <= 0) {
            $request->merge(['area_id' => null]);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required',
            'type' => 'nullable|string',
            'area_id' => 'nullable|exists:areas,id',
            'governorate' => 'nullable|string',
            'city' => 'nullable|string',
            'detailed_address' => 'nullable|string',
            'phone' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $client->update($validated);

        return response()->json($client);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json(['message' => 'Client deleted successfully']);
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ClientsExport, 'clients.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file'
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ClientsImport, $request->file('file'));

        return response()->json(['message' => 'تم استيراد بيانات العملاء والمسئولين بنجاح']);
    }
}
