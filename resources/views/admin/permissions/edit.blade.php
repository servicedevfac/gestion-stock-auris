@extends('layouts.base')

@section('title', 'Modifier Permission')

@section('content')
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient card-heade d-flex justify-content-between align-items-center">
                    <h3 class="header-title text-white"><i class="fas fa-edit me-2"></i>Modifier Permission</h3>
                    <a href="{{ route('permissions.index') }}" class="btn btn-header  fw-bold shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body">

                <form class="needs-validation" action="{{ route('permissions.update', $permission->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nom de la permission</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $permission->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                        <button type="submit" class="btn btn-header1 btn-lg px-5">
                            <i class="fas fa-save me-2"></i> Modifier
                        </button>

                </form>
            </div>
        </div>
    </div>

@endsection
