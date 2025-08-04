@extends('layouts.base')

@section('content')
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header card-heade d-flex justify-content-between align-items-center">
                    <h3 class="text-white m-0"><i class="fas fa-plus me-2"></i>Creer</h3>
                    <a href="{{ route('roles.index') }}" class="btn btn-header fw-bold shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body">
                    <form action="{{ route('roles.store') }}" method="POST">
                        @csrf

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">Nom du rôle</label>
                            <input type="text" name="name" class="form-control form-control-lg border1" required>
                        </div>

                        <div class="mb-4">
                            <label for="permissions" class="form-label fw-bold">Permissions</label>
                            <div class="row g-3">
                                @foreach($permissions as $permission)
                                    <div class="col-md-4">
                                        <div class="form-check custom-checkbox">
                                            <input class="form-check-input border1" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm-{{ $permission->id }}">
                                            <label class="form-check-label text-secondary" for="perm-{{ $permission->id }}">
                                                {{ $permission->name }}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-header1 btn-lg p-2">
                        <i class="fas fa-save me-2"></i>Créer le rôle
                    </button>
                </div>
            </div>
        </div>
    </div>
        </div>


@endsection
