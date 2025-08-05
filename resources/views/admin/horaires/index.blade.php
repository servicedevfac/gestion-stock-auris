@extends('layouts.base')

@section('content')
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient bg-info d-flex justify-content-between align-items-center">
                <h3 class="text-white m-0"><i class="fas fa-list me-2"></i> Liste des ventes</h3>
                <a href="{{ route('ventes.create') }}" class="btn btn-light text-info fw-bold shadow-sm">
                    <i class="fas fa-plus me-1"></i> Nouvelle vente
                </a>
            </div>
            <div class="card-body">
                <a href="{{ route('admin.horaires.edit') }}" class="btn btn-primary mb-3 fw-semibold">
                    <i class="fas fa-clock me-1"></i> Modifier les horaires
                </a>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Jour</th>
                                <th>Heure d'ouverture</th>
                                <th>Heure de fermeture</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($horaires as $horaire)
                                <tr>
                                    <td>{{ ucfirst($horaire->jour_semaine) }}</td>
                                    <td>{{ $horaire->heure_ouverture }}</td>
                                    <td>{{ $horaire->heure_fermeture }}</td>
                                    <td class="text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" href="{{ route('admin.horaires.edit', $horaire->id) }}"><i class="fas fa-edit me-2"></i>Modifier</a></li>

                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
