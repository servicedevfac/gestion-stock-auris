@extends('layouts.base')

@section('title', 'Enregistrer une dépense')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="mb-sm-0 text-dark fw-bold">Enregistrer une nouvelle dépense</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('depenses.index') }}">Dépenses</a></li>
                        <li class="breadcrumb-item active">Nouvelle dépense</li>
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
                        <i class="ri-wallet-3-line text-primary me-2 align-middle"></i>Formulaire de dépense
                    </h5>
                    <a href="{{ route('depenses.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="ri-arrow-left-line me-1"></i>Retour à la liste
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('depenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-3">
                            <!-- Titre -->
                            <div class="col-md-8">
                                <label for="titre" class="form-label fw-semibold text-dark">Libellé / Titre de la dépense <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('titre') is-invalid @enderror" id="titre" name="titre" value="{{ old('titre') }}" placeholder="Ex: Paiement loyer local commercial, Facture CIE, Plein carburant..." required>
                                @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Montant -->
                            <div class="col-md-4">
                                <label for="montant" class="form-label fw-semibold text-dark">Montant (FCFA) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="1" min="1" class="form-control @error('montant') is-invalid @enderror" id="montant" name="montant" value="{{ old('montant') }}" placeholder="50000" required>
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
                                        <option value="{{ $key }}" {{ old('categorie') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Date de la dépense -->
                            <div class="col-md-4">
                                <label for="date_depense" class="form-label fw-semibold text-dark">Date de dépense <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('date_depense') is-invalid @enderror" id="date_depense" name="date_depense" value="{{ old('date_depense', date('Y-m-d')) }}" required>
                                @error('date_depense')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Mode de Paiement -->
                            <div class="col-md-4">
                                <label for="mode_paiement" class="form-label fw-semibold text-dark">Mode de paiement <span class="text-danger">*</span></label>
                                <select class="form-select @error('mode_paiement') is-invalid @enderror" id="mode_paiement" name="mode_paiement" required>
                                    @foreach($modesPaiement as $key => $label)
                                        <option value="{{ $key }}" {{ old('mode_paiement', 'Especes') == $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('mode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Bénéficiaire -->
                            <div class="col-md-6">
                                <label for="beneficiaire" class="form-label fw-semibold text-dark">Bénéficiaire / Fournisseur</label>
                                <input type="text" class="form-control @error('beneficiaire') is-invalid @enderror" id="beneficiaire" name="beneficiaire" value="{{ old('beneficiaire') }}" placeholder="Ex: Propriétaire M. Koffi, CIE, Librairie de France...">
                                @error('beneficiaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Personne, entreprise ou organisme qui reçoit le paiement.</small>
                            </div>

                            <!-- Justificatif (Pièce jointe) -->
                            <div class="col-md-6">
                                <label for="justificatif" class="form-label fw-semibold text-dark">Pièce justificative (Reçu, facture, reçu de virement)</label>
                                <input type="file" class="form-control @error('justificatif') is-invalid @enderror" id="justificatif" name="justificatif" accept=".jpg,.jpeg,.png,.pdf">
                                @error('justificatif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Formats acceptés : JPG, PNG, PDF (Max : 4 Mo).</small>
                            </div>

                            <!-- Description / Notes -->
                            <div class="col-12">
                                <label for="description" class="form-label fw-semibold text-dark">Description / Observations détaillées</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3" placeholder="Détails supplémentaires ou justification de la dépense...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-end gap-2">
                                <a href="{{ route('depenses.index') }}" class="btn btn-light px-4">Annuler</a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="ri-save-line me-1"></i>Enregistrer la dépense
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
