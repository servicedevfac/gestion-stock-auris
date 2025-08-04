@extends('layouts.base')
@section('title', 'Liste des utilisateurs')
@section('content')
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header card-heade d-flex justify-content-between align-items-center">
                    <h3 class="text-white m-0"><i class="fas fa-list me-2"></i> Liste des utilisateurs</h3>
                    <a href="{{ route('users.create') }}" class="btn btn-header fw-bold shadow-sm">
                        <i class="fas fa-plus me-1"></i> Nouvel utilisateur
                    </a>
                </div>
                <div class="card-body">

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>Numero</th>
                                <th>Nom</th>
                                <th>Prénom</th>
                                <th>Rôle</th>
                                <th>Email</th>
                                <th>Téléphone</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $user)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $user->nom }}</td>
                                <td>{{ $user->prenom }}</td>
                                <td>
                                    @forelse($user->getRoleNames() as $role)
                                        <span class="badge bg-primary">{{ $role }}</span>
                                    @empty
                                        <span class="text-muted">Aucun rôle</span>
                                    @endforelse
                                </td>

                                <td>{{ $user->email }}</td>
                                <td>{{ $user->telephone }}</td>
                                <td>
                                    <div class="d-flex justify-content-center gap-3">
                                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-header1 rounded-3">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-success rounded-3">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form id="delete-form-{{ $user->id }}" action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-danger btn-delete" data-form-id="delete-form-{{ $user->id }}">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>




@endsection
