@extends('layouts.base')

@section('content')

<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header card-heade d-flex justify-content-between align-items-center">
                <h3 class="header-title text-white"><i class="fas fa-plus me-2"></i>Nouveau Produit</h3>
                <a href="{{ route('produits.index') }}" class="btn btn-header fw-bold shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour
                </a>
            </div>
            <div class="card-body">
                <p class="sub-header">Remplissez le formulaire pour ajouter un nouveau produit.</p>

                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                <form class="needs-validation" novalidate method="POST" action="{{ route('produits.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom du produit</label>
                        <input type="text" class="form-control @error('nom') is-invalid @enderror" id="nom" name="nom" value="{{ old('nom') }}" required maxlength="255">
                        <div class="invalid-feedback">
                            Veuillez saisir le nom du produit.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="prix" class="form-label">Prix</label>
                        <input type="number" step="0.01" min="0" class="form-control @error('prix') is-invalid @enderror" id="prix" name="prix" value="{{ old('prix') }}" required>
                        <div class="invalid-feedback">
                            Veuillez saisir un prix valide.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="seuil_alerte" class="form-label">Seuil d'alerte</label>
                        <input type="number" min="0" class="form-control @error('seuil_alerte') is-invalid @enderror" id="seuil_alerte" name="seuil_alerte" value="{{ old('seuil_alerte') }}" required>
                        <div class="invalid-feedback">
                            Veuillez saisir un seuil d'alerte valide.
                        </div>
                    </div>
                    <button class="btn btn-header1" type="submit">Créer le produit</button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection