@extends('layouts.base')

@section('content')

    <div>@if($produitsStockFaible->count() > 0)
        <div class="marquee">
            ⚠ Stock faible :
            @foreach($produitsStockFaible as $produit)
                {{ $produit->nom }} ({{ $produit->stock_actuel }}) &nbsp;&nbsp;|&nbsp;&nbsp;
            @endforeach
            veillez à approvisionner ces produits.
        </div>
    @else
            <p>Aucun produit en stock faible ✅</p>
        @endif
    </div>
    <div class="row mt-3">
        <div class="col-lg-6">
            <h4 class="page-title mb-0">Tableau de bord Administrateur</h4>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col" style="flex: 0 0 20%; max-width: 20%">
            <div class="card stat-card card-heade h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold  text-uppercase mb-1">
                                Montant encaissé de la journée
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white text-gray-800">
                                {{ number_format($ca_journalier, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300 text-colj"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="flex: 0 0 20%; max-width: 20%">
            <div class="card   stat-card  success  h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">
                                Montant encaissé du mois en cours
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($chiffreAffaireMoisEnCours, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300 text-colj"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="flex: 0 0 20%; max-width: 20%">
            <div class="card  colj stat-card success  h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-white text-uppercase mb-1">
                                Montant encaissé  annuelles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-white text-gray-800">
                                {{ number_format($chiffreAffaires, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill fa-2x text-gray-300 colj"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="flex: 0 0 20%; max-width: 20%;">
            <div class="card stat-card c h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">
                                Reste à payer
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($totalVentesNonPayes, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300 text-colj"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col" style="flex: 0 0 20%; max-width: 20%;">
            <div class="card stat-card c h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">
                                Chiffre d'affaires total
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($chiffreAffairesGlobaux, 0, ',', ' ') }} XOF
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-shopping-cart fa-2x text-gray-300 text-colj"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- end col-->
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

    </div> <!-- end row-->
    <!-- end row-->
    <div class="row mt-3">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header redoff">
                    <h4 class="card-title">les produits en stock faible</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>N</th>
                                    <th>Nom produit</th>
                                    <th>Stock actuel</th>
                                    <th>Stock minimal</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produitsStockFaible as $produit)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $produit->nom }}</td>
                                        <td>{{ $produit->stock_actuel }}</td>
                                        <td>{{ $produit->seuil_alerte }}</td>
                                        <td>
                                            <a href="{{ route('mouvementStocks.create') }}"
                                                class="btn btn-header1 btn-sm">Approvisionner</a>
                                        </td>
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
                <div class="card-header card-heade">
                    <h4 class="card-title">10 derniers clients</h4>
                </div>
                <div class="card-ody">
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nom</th>
                                    <th>Téléphone</th>
                                    <th>Adresse</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($derniersClients as $derniersClient)
                                    <tr>
                                        <td class="table-user">
                                            {{ $derniersClient->client->nom }}

                                        </td>
                                        <td>
                                            {{ $derniersClient->client->telephone }}
                                        </td>
                                        <td>
                                            {{ $derniersClient->client->adresse }}
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
                <div class="card-header card-heade">
                    <h4 class="card-title">les 10 dernières ventes</h4>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Client</th>
                                    <th>Date de vente </th>
                                    <th>Montant Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($derniersVentes as $vente)
                                    <tr>
                                        <td>{{ $vente->client->nom }}</td>
                                        <td>{{ $vente->created_at }}</td>
                                        <td>{{ $vente->montant_total }}</td>
                                @endforeach
                                </tr>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>
        </div>


@endsection
    @section('scripts')
        <!-- Morris.js et dépendances -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/raphael/2.3.0/raphael.min.js"></script>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($labels);
    const data = @json($data);   // Ligne 1 : CA
    const data1 = @json($data1); // Ligne 2 : Bénéfice ou autre
    const reste=@json($dataReste);

    const ctx = document.getElementById('caLineChart').getContext('2d');

    const caLineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Chiffre d\'affaire ',
                    data: data,
                    backgroundColor: 'blue',
                    borderColor: 'rgba(54, 162, 235, 1)',

                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                },
                                {
                    label: 'Montant encaissé',
                    data: data1,
                   backgroundColor: 'green',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                },
                {
                    label: 'Reste à payer',
                    data: reste,
                    backgroundColor: 'red',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    fill: true,
                    tension: 0.3,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    borderWidth: 2,
                }
            ],
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
                            return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(context.raw);
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

{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('ventesPaiementsChart');

    const ventesPaiementsChart = new Chart(ctx, {
        type: 'bar', // tu peux changer en 'line'
        data: {
            labels: @json($labels),
            datasets: [
                {
                    label: 'Ventes',
                    data: @json($data),
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Paiements',
                    data: @json($data1),
                    backgroundColor: 'rgba(75, 192, 192, 0.6)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Reste à encaisser',
                    data: @json($dataReste),
                    backgroundColor: 'rgba(255, 99, 132, 0.6)',
                    borderColor: 'rgba(255, 99, 132, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: 'Ventes vs Paiements vs Restes à encaisser (12 derniers mois)'
                },
                tooltip: {
                    mode: 'index',
                    intersect: false
                }
            },
            interaction: {
                mode: 'nearest',
                axis: 'x',
                intersect: false
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Montant (FCFA)'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Mois'
                    }
                }
            }
        }
    });
</script> --}}



    @endsection
