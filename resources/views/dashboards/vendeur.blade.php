@extends('layouts.base')
@section('content')



    <!-- end page title -->
<div>@if($produitsStockFaible->count() > 0)
        <div class="marquee">
            Stock faible :
            @foreach($produitsStockFaible as $produit)
                {{ $produit->nom }} ({{ $produit->stock_actuel }}) &nbsp;&nbsp;|&nbsp;&nbsp;
            @endforeach
        </div>
    @else
        <p>Aucun produit en stock faible ✅</p>
    @endif
</div>


    <div class="row mt-3">
        <div class="col-lg-6">
            <h4 class="page-title mb-0">Dashboard Vendeur</h4>
        </div>

    </div>
    <div class="row mt-3">
        <div class="col" style="flex: 0 0 25%; max-width: 25%;max-height:100px">
            <div class="card stat-card success h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">
                                Chiffre d'affaires
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($chiffreAffairesvendeurs, 0, ',', ' ') }} Fr</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="flex: 0 0 25%; max-width: 25%;max-height:100px">
            <div class="card stat-card primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">Chiffre d'affaires du mois
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($chiffreAffaireMoisEnCours, 0, ',', ' ') }} Fr</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="flex: 0 0 25%; max-width: 25%;max-height:100px">
            <div class="card stat-card primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">
                                Nombre de ventes
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($nombreVentes, 0, ',', ' ') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="flex: 0 0 25%; max-width: 25%;max-height:100px">
            <div class="card stat-card primary h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">
                                Chiffre d'affaires de la semaine
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($chiffreAffairesSemaine, 0, ',', ' ') }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300 text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header card-heade  ">
                    <h4 class="card-title">Ventes récentes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>code_recu</th>
                                    <th>Client</th>
                                    <th>Mode de paiement</th>
                                    <th>Date de création</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventes as $vente)
                                    <tr>
                                        <td>
                                            {{ $vente->code_recu }}
                                        </td>
                                        <td>
                                            {{ $vente->client->nom }}
                                        </td>
                                        <td>
                                            {{ $vente->mode_paiement }}
                                        </td>
                                        <td>
                                            {{ $vente->created_at->format('d M Y') }}
                                        </td>
                                @endforeach

                                </tr>

                            </tbody>
                        </table>
                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header card-heade ">
                    <h4 class="card-title">Clients desVentes récentes</h4>

                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>Nom</th>
                                    <th>Address</th>
                                    <th>Telephone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($derniersClients as $client)
                                    <tr>
                                        <td>
                                            {{ $client->client->nom }}
                                        </td>
                                        <td>
                                            {{ $client->client->adresse }}
                                        </td>
                                        <td>
                                            {{ $client->client->telephone }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- end card-body--><!-- end col -->
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header redoff">
                    <h4 class="card-title">les produits en stock faible</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead>
                                <tr>
                                    <th>N</th>
                                    <th>Nom produit</th>
                                    <th>Stock actuel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produitsStockFaible as $produit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $produit->nom }}</td>
                                        <td>{{ $produit->stock_actuel }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <!-- end card-->
        <!-- end row -->


@endsection
