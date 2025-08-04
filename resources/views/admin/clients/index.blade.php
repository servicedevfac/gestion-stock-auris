@extends('layouts.base')
@section('title', 'Liste des Clients')
@section('content')

@section('content')
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header card-heade d-flex justify-content-between align-items-center">
                <h4 class=" text-white mb-0"> <i class="fas fa-list me-3"></i> Liste des clients</h4>
                <a href="{{ route('clients.create') }}" class="btn btn-header fw-bold shadow-sm">
                    <i class="fas fa-plus me-1"></i> Nouveau client
                </a>
            </div>

            <div class="card-body">


    <table class="table table-hover table-bordered dt-responsive nowrap w-100">
        <thead class="table-dark">
            <tr class="align-item-center">
                <th>Code Client</th>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Téléphone</th>
                <th>Adresse</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->code_client }}</td>
                    <td>{{ $client->nom }}</td>
                    <td>{{ $client->prenom }}</td>
                    <td>{{ $client->telephone }}</td>
                    <td>{{ $client->adresse }}</td>
                    <td>
                        <div class="btn-group gap-2">
                            <a href="{{ route('clients.show', $client) }}" class="btn btn-sm btn-header1 rounded-3">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-success rounded-3">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form id="delete-form-{{ $client->id }}" action="{{ route('clients.destroy', $client) }}" method="POST" class="d-inline">
                                @csrf
                                                @method('DELETE')
                                                <button type="button" class="btn btn-sm btn-danger btn-delete rounded-3" data-form-id="delete-form-{{ $client->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>

                                            </form>

                                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{ $clients->links() }} <!-- Pagination links -->
 </div> <!-- end card-body -->
        </div> <!-- end card -->
    </div> <!-- end col -->




@endsection
