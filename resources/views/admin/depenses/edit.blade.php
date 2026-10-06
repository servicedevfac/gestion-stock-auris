@extends('layouts.base')

@section('title', 'Modifier la dépense')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0 text-dark fw-bold">Modifier la dépense : {{ $depense->titre }}</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('depenses.index') }}">Dépenses</a></li>
                        <li class="breadcrumb-item active">Modifier</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm" role="alert">
            <i class="ri-error-warning-line me-2 align-middle"></i>
            <strong>Veuillez corriger les erreurs ci-dessous :</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card border-0 shadow-sm" style="border-radius: 14px;">
                <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0 fw-bold text-dark">
                        <i class="ri-edit-line text-primary me-2 align-middle"></i>Modification de la dépense #{{ $depense->id }}
                    </h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('depenses.show', $depense) }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                            <i class="ri-eye-line me-1"></i>Voir
                        </a>
                        <a href="{{ route('depenses.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                            <i class="ri-arrow-left-line me-1"></i>Retour
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('depenses.update', $depense) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row g-3">
                            <!-- Titre -->
                            <div class="col-md-8">
                                <label for="titre" class="form-label fw-semibold text-dark">Libellé / Titre de la dépense <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre', $depense->titre) }}" required>
                                @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Montant -->
                            <div class="col-md-4">
                                <label for="montant" class="form-label fw-semibold text-dark">Montant (FCFA) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="1" min="1" class="form-control @error('montant') is-invalid @enderror" id="montant" name="montant" value="{{ old('montant', (int)$depense->montant) }}" required>
                                    <span class="input-group-text bg-light text-muted fw-medium">FCFA</span>
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Catégorie -->
                            <div class="col-md-4">
                                <label for="categorie" class="form-label fw-semibold text-dark">Catégorie <span class="text-danger">*</span></label>
                                <select class="form-select @error('categorie') is-invalid @enderror" id="categorie" name="categorie" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $key => $label)
                                        <option value="{{ $key }}" {{ old('categorie', $depense->categorie) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date de la dépense -->
                            <div class="col-md-4">
                                <label for="date_depense" class="form-label fw-semibold text-dark">Date de dépense <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_depense') is-invalid @enderror" id="date_depense" name="date_depense" value="{{ old('date_depense', $depense->date_depense ? $depense->date_depense->format('Y-m-d') : date('Y-m-d')) }}" required>
                                @error('date_depense')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mode de Paiement -->
                            <div class="col-md-4">
                                <label for="mode_paiement" class="form-label fw-semibold text-dark">Mode de paiement <span class="text-danger">*</span></label>
                                <select class="form-select @error('mode_paiement') is-invalid @enderror" id="mode_paiement" name="mode_paiement" required>
                                    @foreach($modesPaiement as $key => $label)
                                        <option value="{{ $key }}" {{ old('mode_paiement', $depense->mode_paiement) == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('mode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bénéficiaire -->
                            <div class="col-md-6">
                                <label for="beneficiaire" class="form-label fw-semibold text-dark">Bénéficiaire / Fournisseur</label>
                                <input type="text" class="form-control @error('beneficiaire') is-invalid @enderror" id="beneficiaire" name="beneficiaire" value="{{ old('beneficiaire', $depense->beneficiaire) }}">
                                @error('beneficiaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Justificatif -->
                            <div class="col-md-6">
                                <label for="justificatif" class="form-label fw-semibold text-dark">Remplacer la pièce justificative</label>
                                <input type="file" class="form-control @error('justificatif') is-invalid @enderror" id="justificatif" name="justificatif" accept=".jpg,.jpeg,.png,.pdf">
                                @error('justificatif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if($depense->justificatif)
                                    <div class="mt-2 text-muted small d-flex align-items-center gap-2">
                                        <i class="ri-attachment-2 text-primary"></i>
                                        <span>Fichier actuel :</span>
                                        <a href="{{ asset($depense->justificatif) }}" target="_blank" class="fw-semibold text-primary text-decoration-underline">Consulter le document</a>
                                    </div>
                                @endif
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold text-dark">Description / Observations</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description', $depense->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                                <a href="{{ route('depenses.index') }}" class="btn btn-light px-4">Annuler</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ri-check-line me-1"></i>Enregistrer les modifications
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
