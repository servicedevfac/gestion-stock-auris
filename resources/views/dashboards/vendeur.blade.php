@extends('layouts.base')
@section('content')



    <!-- end page title -->

    <div class="row mt-5">
        <div class="col-lg-6 col-xl-3">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Chiffre d'affaires</h6>
                            <span class="h3 mb-0">{{ $chiffreAffairesvendeurs }}</span>
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
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Chiffre d'affaires du mois</h6>
                            <span class="h3 mb-0">{{ $chiffreAffaireMoisEnCours }}</span>
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
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Nombre de ventes</h6>
                            <span class="h3 mb-0">{{ $nombreVentes }}</span>
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
                            <h6 class="text-uppercase font-size-12 text-muted mb-3">Chiffre d'affaires de la semaine</h6>
                            <span class="h3 mb-0">{{ $chiffreAffairesSemaine }}</span>
                        </div>
                    </div> <!-- end row -->
                    <div id="sparkline4" class="mt-3"></div>
                </div> <!-- end card-body-->
            </div> <!-- end card-->
        </div> <!-- end col-->
    </div>
    <!-- end row-->


    <!-- end row-->

    <div class="row">
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
                </div> <!-- end card-body--><!-- end col -->
            </div>
        </div> <!-- end card-->
     <!-- end row -->


@endsection
