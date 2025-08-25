@extends('layouts.base')


@section('head')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

@endsection

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $err)
                <li>{{ $err }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row mt-5">
    <div class="col-12">
        <div class="card shadow-sm border-0">
            <div class="card-header card-heade d-flex justify-content-between align-items-center">
                <h3 class="text-white m-0"><i class="fas fa-list me-2"></i> Nouvelle vente</h3>
                <a href="{{ route('ventes.create') }}" class="btn btn-header fw-bold shadow-sm">
                    <i class="fas fa-plus me-1"></i> Nouvelle vente
                </a>
            </div>
            <div class="card-body">
                @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                @endif
                <p>Enregistrez une nouvelle vente en remplissant le formulaire ci-dessous.</p>

                <form method="POST" action="{{ route('ventes.store') }}" novalidate>
                    @csrf

                    {{-- Client --}}
                    <div class="mb-3">
                        <label for="client_id" class="form-label">Client</label>
                        <select id="client-select" name="client_id" class="form-control" required style="width: 100%;">
                            <!-- AJAX rempli par Select2 -->
                        </select>
                    </div>

                    {{-- Tableau produits --}}
                    <table class="table table-bordered" id="produits-table">
                        <thead>
                            <tr>
                                <th>Produit</th>
                                <th>Prix unitaire</th>
                                <th>Quantité</th>
                                <th>Total</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="ligne-produits">
                            <tr>
                                <td>
                                    <select name="produits[0][produit_id]" class="form-control produit-select" required>
                                        <option value="">-- Produit --</option>
                                        @foreach($produits as $produit)
                                        <option value="{{ $produit->id }}" data-prix="{{ $produit->prix }}">
                                            {{ $produit->nom }}
                                        </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td><input type="number" name="produits[0][prix]" class="form-control prix" readonly></td>
                                <td><input type="number" name="produits[0][quantite]" class="form-control quantite" min="1" value="1" required></td>
                                <td><input type="number" class="form-control total" readonly></td>
                                <td><button type="button" class="btn btn-delete btn-sm supprimer-ligne"><i class="fas fa-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>

                    <button type="button" class="btn btn-header1" id="ajouter-ligne">
                        <i class="fas fa-plus me-2"></i> Ajouter un produit
                    </button>

                    {{-- Remise et total --}}
                    <div class="row mt-4">
                        <div class="col-md-4">
                            <label for="remise" class="form-label">Remise (FCFA)</label>
                            <input type="number" name="remise" id="remise" class="form-control" value="0" min="0" step="1">
                        </div>
                        <div class="col-md-4 offset-md-4">
                            <label for="montant_total" class="form-label">Total à payer (FCFA)</label>
                            <input type="number" name="montant_total" id="montant_total" class="form-control" readonly>
                        </div>
                    </div>

                    {{-- Date et paiement --}}
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label for="date_vente" class="form-label">Date de vente</label>
                            <input type="date" name="date_vente" id="date_vente" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label for="mode_paiement" class="form-label">Mode de paiement</label>
                            <select name="mode_paiement" id="mode_paiement" class="form-control" required>
                                <option value="espèces">Espèces</option>
                                <option value="mobile money">Mobile Money</option>
                                <option value="carte">Carte</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-header1 btn-lg mt-4">
                        <i class="fas fa-save me-2"></i> Enregistrer la vente
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection


    @section('scripts')
   

    <script>
        let index = 1;

        function recalculerTotals() {
            let total = 0;

            document.querySelectorAll('#ligne-produits tr').forEach(row => {
                const prix = parseFloat(row.querySelector('.prix') ? .value || 0);
                const quantite = parseFloat(row.querySelector('.quantite') ? .value || 0);
                const ligneTotal = prix * quantite;
                row.querySelector('.total').value = ligneTotal.toFixed(0);
                total += ligneTotal;
            });

            const remise = parseFloat(document.getElementById('remise') ? .value || 0);
            let montantApayer = total - remise;
            if (montantApayer < 0) montantApayer = 0;
            document.getElementById('montant_total').value = montantApayer.toFixed(0);
        }

        document.getElementById('ajouter-ligne').addEventListener('click', function() {
            const table = document.getElementById('ligne-produits');
            const firstRow = table.querySelector('tr');
            const nouvelleLigne = firstRow.cloneNode(true);

            // Réinitialiser les valeurs de la nouvelle ligne
            nouvelleLigne.querySelectorAll('input, select').forEach(el => {
                if (el.name && el.name.includes('produits')) {
                    const base = el.name.split('[')[0];
                    const champ = el.name.substring(el.name.indexOf(']') + 1);
                    el.name = `${base}[${index}]${champ}`;
                }
                if (el.classList.contains('prix') || el.classList.contains('total')) {
                    el.value = '';
                }
                if (el.classList.contains('quantite')) {
                    el.value = 1;
                }
                if (el.tagName === 'SELECT') {
                    el.selectedIndex = 0;
                }
            });

            // Supprimer la nouvelle ligne (bouton)
            nouvelleLigne.querySelector('.supprimer-ligne').addEventListener('click', function(e) {
                const rows = document.querySelectorAll('#ligne-produits tr');
                if (rows.length > 1) {
                    e.target.closest('tr').remove();
                    recalculerTotals();
                }
            });

            // Au changement produit, remplir le prix
            nouvelleLigne.querySelector('.produit-select').addEventListener('change', function(e) {
                const prix = e.target.selectedOptions[0].getAttribute('data-prix');
                const row = e.target.closest('tr');
                row.querySelector('.prix').value = prix;
                recalculerTotals();
            });

            // Modifier quantité recalcul
            nouvelleLigne.querySelector('.quantite').addEventListener('input', recalculerTotals);

            table.appendChild(nouvelleLigne);
            index++;
            recalculerTotals();
        });

        // Initialiser la première ligne : supprimer, changement produit, quantité
        document.querySelector('.supprimer-ligne').addEventListener('click', function(e) {
            const rows = document.querySelectorAll('#ligne-produits tr');
            if (rows.length > 1) {
                e.target.closest('tr').remove();
                recalculerTotals();
            }
        });
        document.querySelector('.produit-select').addEventListener('change', function(e) {
            const prix = e.target.selectedOptions[0].getAttribute('data-prix');
            const row = e.target.closest('tr');
            row.querySelector('.prix').value = prix;
            recalculerTotals();
        });
        document.querySelector('.quantite').addEventListener('input', recalculerTotals);

        // Remise en temps réel
        document.getElementById('remise').addEventListener('input', recalculerTotals);

        // Calcul initial
        recalculerTotals();

    // Init Select2 client
    $('#client-select').select2({
        placeholder: 'Rechercher un client...',
       // minimumInputLength: 1,
        ajax: {
            url: '{{ route("clients.search") }}',
            dataType: 'json',
            delay: 250,
            data: params => ({ q: params.term }),
            processResults: data => ({
                results: data.map(client => ({
                    id: client.id,
                    text: client.nom + ' '+client.prenom+' Telephone:'+client.telephone
                }))
            }),
            cache: true
        }
    });

    </script>
    @endsection
