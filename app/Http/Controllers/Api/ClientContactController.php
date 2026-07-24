<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\ClientContact;
use Illuminate\Http\Request;

class ClientContactController extends Controller
{
    /**
     * Get all contacts for a specific client (hospital/clinic).
     */
    public function index(Request $request, Client $client)
    {
        return response()->json($client->contacts()->latest()->get());
    }

    /**
     * Store a new contact for a client.
     */
    public function store(Request $request, Client $client)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:255',
        ]);

        $contact = $client->contacts()->create($validated);

        return response()->json([
            'message' => 'Client contact added successfully',
            'contact' => $contact
        ], 201);
    }

    /**
     * Update an existing contact.
     */
    public function update(Request $request, ClientContact $contact)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'job_title' => 'nullable|string|max:255',
        ]);

        $contact->update($validated);

        return response()->json([
            'message' => 'Client contact updated successfully',
            'contact' => $contact
        ]);
    }

    /**
     * Remove a contact.
     */
    public function destroy(ClientContact $contact)
    {
        $contact->delete();
        return response()->json([
            'message' => 'Client contact deleted successfully'
        ]);
    }
}
