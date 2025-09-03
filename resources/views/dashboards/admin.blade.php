@extends('layouts.base')

@section('content')

    <div>@if($produitsStockFaible->count() > 0)
        <div class="marquee">
            ⚠ Stock faible :
            @foreach($produitsStockFaible as $produit)
                {{ $produit->nom }} ({{ $produit->stock_actuel }}) &nbsp;&nbsp;|&nbsp;&nbsp;
            @endforeach
            veillez à réapprovisionner ces produits.
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

        <div class="col" style="flex: 0 0 25%; max-width: 25%">
            <div class="card stat-card card-heade h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">

                            <div class="font-weight-bold  text-uppercase mb-1">
                                Chiffre d'affaires de la journée
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
        <div class="col" style="flex: 0 0 25%; max-width: 25%">
            <div class="card   stat-card  success  h-100 py-2">
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
                            <i class="fas fa-money-bill fa-2x text-gray-300 text-colj"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <div class="col" style="flex: 0 0 25%; max-width: 25%">
            <div class="card  colj stat-card success  h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-white text-uppercase mb-1">
                                Chiffre d'affaires annuelles payés
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
        <div class="col" style="flex: 0 0 25%; max-width: 25%;">
            <div class="card stat-card c h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col me-2">
                            <div class="font-weight-bold text-black text-uppercase mb-1">
                                Chiffre d'affaires non payés
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
        {{--
        <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>
        <script>
            const chartData = @json($chartData); // maintenant toujours rempli de 0 au minimum
            const moisLabels = Object.keys(chartData);
            const produits = [...new Set(Object.values(chartData).flatMap(item => Object.keys(item)))];

            const morrisData = moisLabels.map(mois => {
                const row = { y: mois };
                produits.forEach(produit => {
                    row[produit] = chartData[mois][produit] ?? 0;
                });
                return row;
            });

            new Morris.Bar({
                element: 'chart',
                data: morrisData,
                xkey: 'y',
                ykeys: produits,
                labels: produits,
                hideHover: 'auto',
                resize: true,
                barColors: ['#0b62a4', '#7a92a3', '#4da74d', '#afd8f8'],
                ymin: 0,
                ymax: 'auto'
            });
        </script> --}}

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($labels);
    const data = @json($data);   // Ligne 1 : CA
    const data1 = @json($data1); // Ligne 2 : Bénéfice ou autre

    const ctx = document.getElementById('caLineChart').getContext('2d');

    const caLineChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Chiffre d\'affaires non payés',
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
                    label: 'Chiffre d\'affaire  payé',
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




    @endsection
