@extends('layouts.base')

@section('content')

<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header d-flex justify-content-between align-items-center card-heade">
                <h3 class="text-white m-0"><i class="fas fa-list me-2"></i>Détail de Vente</h3>
                <a href="{{ route('ventes.index') }}" class="btn btn-header fw-bold shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour
                </a>
            </div>
            <div class="card-body">
                <p><strong>Code reçu :</strong> {{ $vente->code_recu }}</p>
                <p><strong>Client :</strong> {{ $vente->client->nom ?? '-' }} {{ $vente->client->prenom }}</p>
                <p><strong>Utilisateur :</strong> {{ $vente->user->nom ?? '-' }}</p>
                <p><strong>Date :</strong> {{ $vente->created_at ? $vente->created_at->format('d/m/Y H:i') : '-' }}</p>
                <p><strong>Total :</strong> {{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</p>
                <p><strong>Remise :</strong> {{ number_format($vente->remise, 0, ',', ' ') }} FCFA</p>
                <p><strong>Montant payé :</strong> {{ number_format($vente->montant_paye, 0, ',', ' ') }} FCFA</p>
                <p><strong>Reste à payer :</strong> {{ number_format($vente->reste_a_payer, 0, ',', ' ') }} FCFA</p>
                <p><strong>État de paiement :</strong>
                    @if ($vente->est_paye)
                        <span class="badge bg-success">Payé</span>
                    @else
                        <span class="badge bg-danger">Non payé</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Produits --}}
        <div class="card mt-3">
            <div class="card-header card-heade">
                <h5 class="mb-0 text-white">Produits vendus</h5>
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

        {{-- Paiements --}}
        <div class="card mt-3">
            <div class="card-header card-heade">
                <h5 class="mb-0 text-white">Historique des paiements</h5>
            </div>
            <div class="card-body">
                @if($vente->paiements->count() > 0)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Montant</th>
                                <th>Mode</th>
                                <th>Reste à payer</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($vente->paiements as $paiement)
                                <tr>
                                    <td>{{ $paiement->date_paiement }}</td>
                                    <td>{{ number_format($paiement->montant, 0, ',', ' ') }} FCFA</td>
                                    <td>{{ ucfirst($paiement->mode_paiement) }}</td>
                                    <td>{{ number_format($paiement->reste_a_payer, 0, ',', ' ') }} FCFA</td>
                                    <td>
                                        <a href="{{ route('paiements.ticket', $paiement->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-print"></i> Ticket
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>Aucun paiement enregistré pour cette vente.</p>
                @endif

                {{-- Formulaire d’ajout d’un paiement si pas encore payé --}}
                @if (!$vente->est_paye)
                    <hr>
                    <h6>Ajouter un paiement :</h6>
                    <form action="{{ route('paiements.store', $vente->id) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="montant">Montant</label>
                            <input type="number" step="0.01" name="montant" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="mode_paiement">Mode de paiement</label>
                            <select name="mode_paiement" class="form-select" required>
                                <option value="">-- Choisir --</option>
                                <option value="espèces">Espèces</option>
                                <option value="mobile_money">Mobile Money</option>
                                <option value="carte">Carte bancaire</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Ajouter paiement</button>
                    </form>
                @endif
            </div>
        </div>

@endsection
