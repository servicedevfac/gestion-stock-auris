@extends('layouts.base')


@section('content')
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header card-heade d-flex justify-content-between align-items-center">
                <h3 class="text-white m-0"><i class="fas fa-user me-3"></i> Détails du client</h3>
                <a href="{{ route('clients.index') }}" class="btn btn-header fw-bold shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body p-4">
                <dl class="row">
                    <dt class="col-sm-3">Nom</dt>
                    <dd class="col-sm-9">{{ $client->nom }}</dd>

                    <dt class="col-sm-3">Prénom</dt>
                    <dd class="col-sm-9">{{ $client->prenom }}</dd>

                    <dt class="col-sm-3">Téléphone</dt>
                    <dd class="col-sm-9">{{ $client->telephone }}</dd>

                    <dt class="col-sm-3">Adresse</dt>
                    <dd class="col-sm-9">{{ $client->adresse }}</dd>
                </dl>
                @can('edit client')
                    <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-header1 me-2">
                        <i class="fas fa-edit me-1"></i> Modifier
                    </a>
                @endcan
            </div>
        </div>
    </div>

@endsection
