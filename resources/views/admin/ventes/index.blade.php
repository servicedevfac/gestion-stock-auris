@extends('layouts.base')
@section('title', 'Liste des ventes')
@section('content')
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header  d-flex justify-content-between align-items-center card-heade">
                <h3 class="text-white m-0"><i class="fas fa-list me-2"></i> Liste des ventes</h3>
                <a href="{{ route('ventes.create') }}" class="btn btn-header  fw-bold shadow-sm">
                    <i class="fas fa-plus me-1"></i> Nouvelle vente
                </a>
            </div>

            <div class="card-body">

<<<<<<< HEAD

                <form method="GET" action="" class="mb-3">
                    <div class="row">
                        <div class="col-md-3">
                            <label>Période</label>
                            <select name="periode" class="form-control" id="periode-select">
                                <option value="jour" @if(request('periode')=='jour' ) selected @endif>Jour</option>
                                <option value="semaine" @if(request('periode')=='semaine' ) selected @endif>Semaine</option>
                                <option value="mois" @if(request('periode')=='mois' ) selected @endif>Mois</option>
                                <option value="annee" @if(request('periode')=='annee' ) selected @endif>Année</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Date de début</label>
                            <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Date de fin</label>
                            <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
                        </div>
                        <div class="col-md-3">
                            <label>Recherche</label>
                            <input type="text" name="q" class="form-control" placeholder="Client, code reçu, utilisateur..." value="{{ request('q') }}">
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-12 d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">Filtrer</button>
                        </div>
                    </div>
                </form>
                @if (session()->has('error'))
                <div class="alert alert-danger">
                    {{ session()->get('error') }}
                </div>
                @endif
=======
    <form method="GET" action="" class="mb-3">
        <div class="row">
            <div class="col-md-3">
                <label>Période</label>
                <select name="periode" class="form-control" id="periode-select">
                    <option value="jour" @if(request('periode')=='jour') selected @endif>Jour</option>
                    <option value="semaine" @if(request('periode')=='semaine') selected @endif>Semaine</option>
                    <option value="mois" @if(request('periode')=='mois') selected @endif>Mois</option>
                    <option value="annee" @if(request('periode')=='annee') selected @endif>Année</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Date de début</label>
                <input type="date" name="date_debut" class="form-control" value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-3">
                <label>Date de fin</label>
                <input type="date" name="date_fin" class="form-control" value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-3">
                <label>Recherche</label>
                <input type="text" name="q" class="form-control" placeholder="Client, code reçu, utilisateur..." value="{{ request('q') }}">
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-12 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Filtrer</button>
            </div>
        </div>
    </form>
    @if (session()->has('error'))
    <div class="alert alert-danger">
        {{ session()->get('error') }}
    </div>
    @endif
>>>>>>> djuedev

                <table class="table table-hover table-bordered dt-responsive nowrap w-100">
                    <thead class="card-heade  table-dark">
                        <tr>
                            <th>Numéro</th>
                            <th>Code reçu</th>
                            <th>Client</th>
                            <th>Date</th>
                            <th>Montant total</th>
                            <th>Remise</th>
                            <th>Recu de vente</th>
                            <th>Actions</th>


                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventes as $vente)
                        <tr @if ($vente->statut=='valide') style="background-color:#d4edda;"
                            @elseif ($vente->statut=='annulee') style="background-color:#f8d7da ;"

                            @else style="background-color:#e2e3e5 ;"
                            @endif>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $vente->code_recu }}</td>
                            <td>{{ $vente->client->nom ?? '' }}</td>

                            {{-- <td>
                    @if ($vente->statut=='valide')
                        <span class="badge bg-success">Validée</span>
                    @elseif ($vente->statut=='annulee')
                        <span class="badge bg-danger">Annulée</span>
                    @else
                        <span style="background-color:#214ea7 ;">{{ $vente->statut }}</span>
                            @endif

                            </td> --}}
                            <td>{{ $vente->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($vente->montant_total, 0, ',', ' ') }} FCFA</td>
                            <td>{{ number_format($vente->remise, 0, ',', ' ') }} FCFA</td>
                            <td>
                                @if($vente->code_recu)
                                <a href="{{ asset('storage/recus/recu_vente_'.$vente->client->nom.'_'.$vente->code_recu.'.pdf') }}" class="btn btn-header1 btn-lg" target="_blank"><i class="fas fa-file-pdf"></i></a>
                                @else
                                <span class="text-muted">Pas de reçu</span>
                                @endif
                            </td>
                            <td style="display:flex;flex-direction:row;justify-content:center; gap: 5px; ">
                                <a href="{{ route('ventes.show', $vente->id) }}" class="btn btn-header1 text-white-bold btn-lg rounded-3"><i class="fas fa-eye"></i></a>
                                @can('Modifier / annuler vente')
                                <form id="form-annuler-{{ $vente->id }}" action="{{ route('ventes.annuler', $vente->id) }}" method="POST">
                                    @csrf
                                    <button type="button" class="btn btn-delete btn-lg" onclick="confirmerAnnulation({{ $vente->id }})"><i class="fas fa-cancel"></i></button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                {{ $ventes->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    <script>
        function confirmerAnnulation(id) {
            Swal.fire({
                title: 'Êtes-vous sûr ?'
                , text: "Vous allez annuler cette vente !"
                , icon: 'danger'
                , showCancelButton: true
                , confirmButtonColor: '#d33'
                , cancelButtonColor: '#3085d6'
                , confirmButtonText: 'Oui, annuler'
                , cancelButtonText: 'Non'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('form-annuler-' + id).submit();
                }
            });
        }

    </script>

    @endsection
