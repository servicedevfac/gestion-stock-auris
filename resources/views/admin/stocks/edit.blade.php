@extends('layouts.base')

@section('content')
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header card-heade d-flex justify-content-between align-items-center">
                    <h3 class="text-white m-0"><i class="fas fa-list me-2"></i>  Liste des mouvements de stock</h3>
                    <a href="{{ route('mouvementStocks.create') }}" class="btn btn-header fw-bold shadow-sm">
                        <i class="fas fa-arrow-left me-1"></i> Retour
                    </a>
                </div>
                <div class="card-body">

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <form method="POST" action="{{ route('mouvementStocks.update', $mouvementStock->id) }}">
                        @csrf
                        @method('PUT')

        <div class="mb-3">
            <label>Produit</label>
            <select name="produit_id" class="form-control" required>
                <option value="">-- Choisir un produit --</option>
                @foreach($produits as $produit)
                    <option value="{{ $produit->id }}" {{ $mouvementStock->produit_id == $produit->id ? 'selected' : '' }}>
                        {{ $produit->nom}}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label>Utilisateur</label>
            <select name="user_id" class="form-control" required>
                <option value="">-- Choisir un utilisateur --</option>
                @foreach($users as $user)
                    <option value="{{ $user->id }}" {{ $mouvementStock->user_id == $user->id ? 'selected' : '' }}>
                        {{ $user->nom }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label>Type de mouvement</label>
            <select name="type_mouvement" class="form-control" required>
                <option value="entree" {{ $mouvementStock->type_mouvement == 'entree' ? 'selected' : '' }}>Entrée</option>
                <option value="sortie" {{ $mouvementStock->type_mouvement == 'sortie' ? 'selected' : '' }}>Sortie</option>
            </select>
        </div>

        <div class="mb-3">
            <label>Quantité</label>
            <input type="number" name="quantite" class="form-control" required min="1" value="{{ old('quantite', $mouvementStock->quantite) }}">
        </div>

        <div class="mb-3">
            <label>Motif</label>
            <input type="text" name="motif" class="form-control" required value="{{ old('motif', $mouvementStock->motif) }}">
        </div>

        <div class="mb-3">
            <label>Date du mouvement</label>
            <input type="date" name="date_mouvement" class="form-control" value="{{ old('date_mouvement', $mouvementStock->date_mouvement) }}" >
        </div>

        <button type="submit" class="btn btn-header1">Mettre à jour</button>
    </form>
</div>
</div>
</div>

@endsection
