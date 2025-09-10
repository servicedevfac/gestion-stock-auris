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
            <h4 class="page-title mb-0"> Tableau de bord gestionnaire </h4>
        </div>
    </div>
    <div class="row mt-3 ">
        <div class="col mb-2" style="flex: 0 0 25%; max-width: 25%;max-height:100px">
            <div class="card stat-card succes   h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">
                                 Chiffre d'affaires payés de la journée
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($ca_journalier, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300 text-success  "></i>
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
                               Chiffre d'affaires payés du mois en cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($chiffreAffaireMoisEnCours, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300 text-success  "></i>
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
                                Chiffre d'affaires annuelles payés
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($chiffreAffairesvendeurs, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300 text-success  "></i>
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
                                chiffre d'affaire global
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{number_format($chiffreAffairesvendeursglobal, 0, ',', ' ')}} XOF

                            </div>
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
                                Chiffre d'affaires impayés de la journée
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 text-danger">
                                {{number_format($ca_journalierNonPaye, 0, ',', ' ')}} XOF

                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar fa-2x text-gray-300 text-danger  "></i>
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
                                chiffre d'affaire impayé du mois en cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 text-danger">
                                 {{number_format($chiffreAffaireMoisEnCourNonPaye, 0, ',', ' ')}} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar fa-2x text-gray-300 text-danger  "></i>
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
                                chiffre d'affaire annuel non payé
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 text-danger">
                                {{number_format($chiffreAffairesvendeursimpaye, 0, ',', ' ')}} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar fa-2x text-gray-300 text-danger  "></i>
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
                                chiffre d'affaire annuel non payé
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 text-danger">
                                {{number_format($chiffreAffairesvendeursimpaye, 0, ',', ' ')}} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar fa-2x text-gray-300 text-danger  "></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
         <div class="row mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-heade">
                    <h4 class="card-title">Chiffre d'affaires des 12 derniers mois </h4>
                </div>
                <div class="chart-container">
                    <canvas id="caLineChart" width="800" height="350"></canvas>
                </div>
            </div>
        </div>

    </div>

    <div class="row mt-4">
        <div class="col-xl-12">
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
                                    </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>

                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col -->

        <div class="col-xl-6">
            <div class="card">
                <div class="card-header card-heade ">
                    <h4 class="card-title">Clients récents</h4>

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
                <!-- end card--><!-- end col -->
            </div>
        </div>

        <!-- end card-->
        <!-- end row -->

    </div>
@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($labels);
    const data = @json($data);
    const data1 = @json($data1);

    const ctx = document.getElementById('caLineChart').getContext('2d');

    const caLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
               {
                    label: 'Chiffre d\'affaires payés',
                    data: data,
                    borderColor: '#02228b',
                    backgroundColor: '#e6b82359',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                },
                {
                    label: 'Chiffre d\'affaire non payé',
                    data: data1,
                    borderColor: '#e62323',
                    backgroundColor: '#e6232359',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return 'CA: ' + new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(context.raw);
                        }
                    }
                }
            },
            interaction: { mode: 'nearest', intersect: false },
            scales: {
                x: {
                    display: true,
                    title: { display: true, text: 'Mois' }
                },
                y: {
                    display: true,
                    title: { display: true, text: 'Montant' },
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value);
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
