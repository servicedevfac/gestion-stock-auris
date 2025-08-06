<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ClientController extends Controller
{

    public function index()
    {
        $clients = Client::paginate(15);
        return view('admin.clients.index', compact('clients'));
    }


    public function create()
    {
        return view('admin.clients.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        // Transaction pour éviter les doublons
        DB::transaction(function () use ($request) {
            $now = now(); // récupère la date actuelle (avec fuseau horaire Laravel)
            $prefix = 'CLI-' . $now->format('Ym') . '-'; // ex : CLI-202507-

            // On cherche le dernier client du mois en cours
            $lastClient = Client::where('code_client', 'like', $prefix . '%')
                ->lockForUpdate() // évite les conflits quand plusieurs clients sont créés en même temps
                ->orderByDesc('code_client')
                ->first();

            $nextNumber = 1;
            if ($lastClient) {
                // Récupère le numéro de fin et l'incrémente
                $lastSuffix = (int) Str::afterLast($lastClient->code_client, '-');
                $nextNumber = $lastSuffix + 1;
            }

            // Génère le nouveau code client
            $codeClient = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Ajoute le code généré aux données du formulaire
            $request->merge(['code_client' => $codeClient]);

            // Enregistre le client
            Client::create($request->all());
        });

        return redirect()->route('clients.index')->with('success', 'Client créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Client $client)
    {
        return view('admin.clients.show', compact('client'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Client $client)
    {
        return view('admin.clients.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Client $client)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'nu llable|string|max:20',
            'adresse' => 'nullable|string|max:255',
        ]);

        $client->update($request->all());

        return redirect()->route('clients.index')->with('success', 'Client mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Client $client)
    {
        $client->delete();

        return redirect()->route('clients.index')->with('success', 'Client supprimé avec succès.');
    }
    
    public function search(Request $request)
    {
        $search = $request->get('q');
        $clients = Client::where('nom', 'like', "%$search%")->get(['id', 'nom']);
        return response()->json($clients);
    }
}
