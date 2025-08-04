@extends('layouts.base')
@section('content')
<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header card-heade d-flex justify-content-between align-items-center">
                <h3 class="text-white m-0"><i class="fas fa-list me-2"></i>Modifier un utilisateur</h3>
                <a href="{{ route('users.index') }}" class="btn btn-header fw-bold shadow-sm">
                    <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                </a>
            </div>
            <div class="card-body">
                @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
                @endif
                @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
                <form action="{{ route('users.update', $user->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="nom" class="form-label">Nom</label>
                        <input type="text" class="form-control" id="nom" name="nom" value="{{ $user->nom }}">
                    </div>

                    <div class="mb-3">
                        <label for="prenom" class="form-label">Prénom</label>
                        <input type="text" class="form-control" id="prenom" name="prenom" value="{{ $user->prenom }}">
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Adresse email</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}">
                    </div>

                    <div class="form-group mb-3">
                        <label for="password">Nouveau mot de passe (laisser vide si inchangé)</label>
                        <input type="password" name="password" class="form-control">
                    </div>

                    <div class="form-group mb-3">
                        <label for="password_confirmation">Confirmer le mot de passe</label>
                        <input type="password" name="password_confirmation" class="form-control">
                    </div>

                    <div class="mb-3">
                        <label for="telephone" class="form-label">Numéro de téléphone</label>
                        <input type="tel" class="form-control" id="telephone" name="telephone" value="{{ $user->telephone }}">
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Rôle</label>
                        <select class="form-select" id="role" name="role">
                            @foreach($roles as $role)
                            <option value="{{ $role->name }}" {{ $user->role === $role->name ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
<<<<<<< HEAD
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-header1"><i class="fas fa-save me-2"></i> Mettre à jour</button>
=======
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-header1 btn-lg"><i class="fas fa-save me-2"></i> Mettre à jour</button>
>>>>>>> djuedev

                </form>
            </div>
        </div>
    </div>
</div>

@endsection
