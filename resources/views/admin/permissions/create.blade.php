@extends('layouts.base')

@section('content')
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header card-heade d-flex justify-content-between align-items-center">
                    <h3 class="header-title text-white"><i class="fas fa-plus me-2"></i>Nouvelle Permission</h3>
                     <a href="{{ route('permissions.index') }}" class="btn btn-header shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
                </div>
                <div class="card-body">

                <form action="{{ route('permissions.store') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="form-group">
                            <label for="name">Nom Permission</label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                            @error('name')
                                <span class="invalid-feedback">{{ $message }}</span>
                            @enderror
                        </div>



                    </div>

                        <button type="submit" class="btn btn-header1 btn-lg p-2">
                            <i class="fas fa-save "></i> Créer la permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
<<<<<<< HEAD
    </div>
=======



>>>>>>> djuedev
@endsection
