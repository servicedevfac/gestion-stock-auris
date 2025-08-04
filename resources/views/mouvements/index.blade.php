@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Liste des mouvements de stock</h1>
    <a href="{{ route('mouvementStocks.create') }}" class="btn btn-primary mb-3">Ajouter un mouvement</a>
<<<<<<< HEAD
    @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
    @endif
=======

>>>>>>> djuedev
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Produit</th>
                <th>Type</th>
                <th>Quantité</th>
                <th>Stock Actuel</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($mouvementStocks as $mouvement)
            <tr>
                <td>{{ $mouvement->id }}</td>
                <td>{{ $mouvement->produit->nom ?? 'N/A' }}</td>
                <td>{{ ucfirst($mouvement->type) }}</td>
                <td>{{ $mouvement->quantite }}</td>
                <td>{{ $mouvement->produit->stock_actuel ?? 'N/A' }}</td>
                <td>{{ $mouvement->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('mouvementStocks.show', $mouvement) }}" class="btn btn-info btn-sm">Voir</a>
                    <a href="{{ route('mouvementStocks.edit', $mouvement) }}" class="btn btn-warning btn-sm">Modifier</a>
                    <form action="{{ route('mouvementStocks.destroy', $mouvement) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ce mouvement ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center">Aucun mouvement trouvé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    {{ $mouvementStocks->links() }}
</div>
@endsection