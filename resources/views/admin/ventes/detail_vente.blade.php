@extends('layouts.base')

@section('title', 'Détail de la vente')

@section('content')

    <div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header  d-flex justify-content-between align-items-center card-heade">
                <h3 class="text-white m-0"><i class="fas fa-list me-2"></i>Detail de Ventes</h3>
                <a href="{{ route('ventes.index') }}" class="btn btn-header  fw-bold shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
        <div class="card-body">
            <p><strong>Code reçu :</strong> {{ $vente->code_recu }}</p>
            <p><strong>Client :</strong> {{ $vente->client->nom ?? '-' }}</p>
            <p><strong>Utilisateur :</strong> {{ $vente->user->nom ?? '-' }}</p>
            <p><strong>Date :</strong> {{ $vente->created_at ? $vente->created_at->format('d/m/Y H:i') : '-' }}</p>
            <p><strong>Mode de paiement :</strong> {{ $vente->mode_paiement }}</p>
            <p><strong>Remise :</strong> {{ number_format($vente->remise, 0, ',', ' ') }} FCFA</p>
            <p><strong>Total payé :</strong> {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</p>
            <p><strong>Status :</strong><span class="text-white badge bg-success">{{ $vente->statut }}</span> </p>
        </div>
    </div>
    <div class="card">
        <div class="card-header card-heade ">
            <h5 class="mb-0 text-white">Produits vendus</h5>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0 ">
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
    </div>



@endsection
