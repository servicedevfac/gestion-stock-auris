@extends('layouts.base')

@section('content')
<div class="container py-5">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0 text-white"><i class="fas fa-clock me-2"></i> Modifier les horaires</h4>
        </div>

        <div class="card-body">
            <form action="{{ route('admin.horaires.update') }}" method="POST">
                @csrf

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Jour</th>
                                <th>Heure d'ouverture</th>
                                <th>Heure de fermeture</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($jours as $jour)
                            <tr>
                                <td class="fw-semibold">{{ ucfirst($jour) }}</td>
                                <td>
                                    <input type="time" class="form-control" name="heure_ouverture[{{ $jour }}]" value="{{ $horaires[$jour]->heure_ouverture ?? '' }}" required>
                                </td>
                                <td>
                                    <input type="time" class="form-control" name="heure_fermeture[{{ $jour }}]" value="{{ $horaires[$jour]->heure_fermeture ?? '' }}" required>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-info px-4">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
