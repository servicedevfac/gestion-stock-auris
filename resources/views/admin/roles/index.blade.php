@extends('layouts.base')

@section('content')
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header card-heade d-flex justify-content-between align-items-center">
                <h3 class="text-white m-0"><i class="fas fa-list me-2"></i> Liste des rôles</h3>
                <a href="{{ route('roles.create') }}" class="btn btn-header fw-bold shadow-sm">
                    <i class="fas fa-plus me-1"></i> Nouveau rôle
                </a>
            </div>
            <div class="card-body">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered dt-responsive nowrap w-100">
                        <thead class="table-dark">
                            <tr>
                                <th>N°</th>
                                <th>Nom</th>
                                <th>Permission</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($roles as $role)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $role->name }}</td>
                                <td>
                                    @foreach($role->permissions as $permission)
                                    <span class="badge card-heade">{{ $permission->name }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    <div style="display:flex;flex-direction:row;justify-content:end; gap: 5px; ">

                                        <a href="{{ route('roles.show', $role->id) }}" class="btn btn-header1 btn-sm rounded-3" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-success  btn-sm rounded-3" title="Modifier">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST" onsubmit="return confirm('Voulez-vous vraiment supprimer ce rôle ?')" class="d-inline rounded-3">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm rounded-3" title="Supprimer">
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
    </div>
</div>

@endsection