@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Créer un Mouvement de Stock</h2>
    <form action="{{ route('mouvementStocks.store') }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="produit_id" class="form-label">Produit</label>
            <select name="produit_id" id="produit_id" class="form-control" required>
                @foreach($produits as $produit)
                <option value="{{ $produit->id }}">{{ $produit->nom }} ({{ $produit->prix }} FCFA)</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="id_client" class="form-label">Client</label>
            <select name="id_client" id="id_client" class="form-control">
                @foreach($clients as $client)
                <option value="{{ $client->id }}">{{ $client->nom }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label for="quantite" class="form-label">Quantité</label>
            <input type="number" name="quantite" id="quantite" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">Type</label>
            <select name="type" id="type" class="form-control" required>
                <option value="entrée">Entrée</option>
                <option value="sortie">Sortie</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="date" class="form-label">Date</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="motif" class="form-label">Motif</label>
            <input type="text" name="motif" id="motif" class="form-control">
        </div>

        <button type="submit" class="btn btn-primary">Enregistrer</button>
    </form>
</div>
@endsection
