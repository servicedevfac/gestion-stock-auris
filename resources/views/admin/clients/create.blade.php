@extends('layouts.base')
@section('content')

<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header card-heade d-flex justify-content-between align-items-center">
                <h3 class="text-white m-0"><i class="fas fa-plus me-3"></i> Créer un client</h3>
                <a href="{{ route('clients.index') }}" class="btn btn-header fw-bold shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
            </div>
            <div class="card-body">
                <form action="{{ route('clients.store') }}" method="POST" class="needs-validation" novalidate>
                    @csrf

                    <div class="form-floating mb-3">
                        <input type="text" name="nom" id="nom" value="{{ old('nom') }}" class="form-control @error('nom') is-invalid @enderror" placeholder="Nom" required>
                        <label for="nom">Nom *</label>
                        @error('nom')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" name="prenom" id="prenom" value="{{ old('prenom') }}" class="form-control @error('prenom') is-invalid @enderror" placeholder="Prénom">
                        <label for="prenom">Prénom</label>
                        @error('prenom')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-3">
                        <input type="tel" name="telephone" id="telephone" value="{{ old('telephone') }}" class="form-control @error('telephone') is-invalid @enderror" placeholder="Téléphone" required>
                        <label for="telephone">Téléphone *</label>
                        @error('telephone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-floating mb-4">
                        <input type="text" name="adresse" id="adresse" value="{{ old('adresse') }}" class="form-control @error('adresse') is-invalid @enderror" placeholder="Adresse">
                        <label for="adresse">Adresse</label>
                        @error('adresse')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

<<<<<<< HEAD
                    <div class="text-end">
                        <button type="submit" class="btn btn-header1 btn-sm px-5">
                            <i class="fas fa-save me-2"></i>Enregistrer le client
                        </button>
                    </div>
                </form>
=======
                            <button type="submit" class="btn btn-header1 btn-lg px-2">
                                <i class="fas fa-save me-2"></i>Enregistrer le client
                            </button>

                    </form>
                </div>
>>>>>>> djuedev
            </div>
        </div>

<<<<<<< HEAD
    </div>
</div>
=======


>>>>>>> djuedev
@endsection
