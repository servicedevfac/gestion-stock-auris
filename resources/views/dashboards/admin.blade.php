@extends('layouts.base')
@section('title', 'Dashboard Administrateur')
@section('content')


                <div class="row mt-3">
                    <div class="col-lg-6">
                        <h4 class="page-title mb-0">Dashboard Administrateur</h4>
                    </div>

                </div>


            <div class="row mt-3">
                <div class="col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h5 class="text-uppercase font-size-12 text-muted mb-3">Chiffre d'Affaires</h5>
                                    <span class="h3 mb-0">{{ (number_format($chiffreAffaires, 0, ',', ' ')) }} Fr</span>
                                </div>

                            </div> <!-- end row -->

                            <div id="sparkline1" class="mt-3"></div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-uppercase font-size-12 text-muted mb-3">CA du mois en cours</h6>
                                    <span class="h3 mb-0">{{ number_format($chiffreAffaireMoisEnCours, 0, ',', ' ') }}
                                    </span>
                                </div>
                            </div> <!-- end row -->

                            <div id="sparkline2" class="mt-3"></div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-uppercase font-size-12 text-muted mb-3">chiffre d'affaires journalier
                                    </h6>
                                    <span class="h3 mb-0">{{ (number_format($ca_journalier, 0, ',', ' ')) }} Fr</span>
                                </div>
                                <div class="col-auto">

                                </div>
                            </div> <!-- end row -->

                            <div id="sparkline3" class="mt-3"></div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->

                <div class="col-lg-6 col-xl-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h6 class="text-uppercase font-size-12 text-muted mb-3">TOTAL VENTES REALISEES</h6>
                                    <span class="h3 mb-0">{{ number_format($nombreVentes, 0, ',', ' ') }}</span>
                                </div>
                            </div> <!-- end row -->

                            <div id="sparkline4" class="mt-3"></div>
                        </div> <!-- end card-body-->
                    </div> <!-- end card-->
                </div> <!-- end col-->
            </div>
            <!-- end row-->

                <div class="row mt-3">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-header card-heade">
                                <h4 class="card-title">Chiffre d'affaires par produit (par mois)</h4>
                            </div>
                            <div class="card-body">
                                <div id="chart" style="height: 300px;"></div><!-- end col-->
                            </div>
                        </div>
                    </div>

                </div> <!-- end row-->

                <div class="row mt-3">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header card-heade">
                                <h4 class="card-title">Derniers clients ayant acheté</h4>
                            </div>
                            <div class="card-ody">
                                <div class="table-responsive">
                                    <table class="table table-centered table-striped table-nowrap mb-0">
                                        <thead>
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
                                <h4 class="card-title">les 5 dernieres ventes</h4>
                            </div>

                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-hover table-centered table-nowrap mb-0">
                                        <thead>
                                            <tr>
                                                <th>Code de Recu</th>
                                                <th>Date de vente </th>
                                                <th>Remise</th>
                                                <th>Montant Total</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($derniersVentes as $vente)
                                                <tr>
                                                    <td>{{ $vente->code_recu }}</td>
                                                    <td>{{ $vente->created_at }}</td>
                                                    <td>{{ $vente->remise}}</td>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

    <script>
        const chartData = @json($chartData);

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
            ymin: 0, // Définir l'origine de l'axe Y à 0 (modifiable)
            ymax: 'auto' // Vous pouvez remplacer 'auto' par une valeur numérique pour fixer l'échelle max
        });
    </script>



@endsection
