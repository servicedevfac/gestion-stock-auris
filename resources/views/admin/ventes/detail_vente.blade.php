@extends('layouts.base')
@section('content')

<h4>Détail de la vente</h4>
<div class="card mb-3">
    <div class="card-body">
        <p><strong>Code reçu :</strong> {{ $vente->code_recu }}</p>
        <p><strong>Client :</strong> {{ $vente->client->nom ?? '-' }}</p>
        <p><strong>Utilisateur :</strong> {{ $vente->user->nom ?? '-' }}</p>
        <p><strong>Date :</strong> {{ $vente->date_vente }}</p>
        <p><strong>Mode de paiement :</strong> {{ $vente->mode_paiement }}</p>
        <p><strong>Remise :</strong> {{ number_format($vente->remise, 0, ',', ' ') }} FCFA</p>
        <p><strong>Total payé :</strong> {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</p>
    </div>
</div>
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Produits vendus</h5>
    </div>
    <div class="card-body p-0">
        <table class="table table-bordered mb-0">
            <thead>
                <tr>
                    <th>Produit</th>
                    <th>Prix unitaire</th>
                    <th>Quantité</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($vente->details as $detail)
                <tr>
                    <td>{{ $detail->produit->nom ?? '-' }}</td>
                    <td>{{ number_format($detail->prix, 0, ',', ' ') }} FCFA</td>
                    <td>{{ $detail->quantite }}</td>
                    <td>{{ number_format($detail->prix * $detail->quantite, 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
<a href="{{ route('ventes.index') }}" class="btn btn-secondary mt-3">Retour à la liste</a>

@endsection
