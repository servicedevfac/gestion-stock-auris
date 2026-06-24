@extends('layouts.base')
@section('content')

    {{-- Stock faible alert --}}
    @if($produitsStockFaible->count() > 0)
        <div class="marquee">
            ⚠ Stock faible :
            @foreach($produitsStockFaible as $produit)
                {{ $produit->nom }} ({{ $produit->stock_actuel }}) &nbsp;&nbsp;|&nbsp;&nbsp;
            @endforeach
        </div>
    @endif

    <div class="row mt-3">
        <div class="col-lg-6">
            <h4 class="page-title mb-0">Tableau de bord gestionnaire</h4>
        </div>
    </div>

    {{-- Stat cards --}}
    <div class="row mt-3 gx-3 gy-3">
        <div class="col-sm-6 col-lg-3">
            <div class="card modern-stat stat-green">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Encaissé / jour</div>
                            <div class="stat-value">{{ number_format($ca_journalier, 0, ',', ' ') }} <small>XOF</small></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card modern-stat stat-blue">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Encaissé / mois</div>
                            <div class="stat-value">{{ number_format($chiffreAffaireMoisEnCours, 0, ',', ' ') }} <small>XOF</small></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card modern-stat stat-gold">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Encaissé / an</div>
                            <div class="stat-value">{{ number_format($chiffreAffairesvendeurs, 0, ',', ' ') }} <small>XOF</small></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card modern-stat stat-purple">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Chiffre d'affaires</div>
                            <div class="stat-value">{{ number_format($chiffreAffairesvendeursglobal, 0, ',', ' ') }} <small>XOF</small></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-wallet"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card modern-stat stat-red">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Reste à payer / jour</div>
                            <div class="stat-value">{{ number_format($ca_journalierNonPaye, 0, ',', ' ') }} <small>XOF</small></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card modern-stat stat-red">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Reste à payer / mois</div>
                            <div class="stat-value">{{ number_format($chiffreAffaireMoisEnCourNonPaye, 0, ',', ' ') }} <small>XOF</small></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="card modern-stat stat-red">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <div class="stat-label">Reste à payer / an</div>
                            <div class="stat-value">{{ number_format($chiffreAffairesvendeursimpaye, 0, ',', ' ') }} <small>XOF</small></div>
                        </div>
                        <div class="stat-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Chart --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header card-heade">
                    <h4 class="card-title"><i class="fas fa-chart-bar me-2"></i>Chiffre d'affaires des 12 derniers mois</h4>
                </div>
                <div class="chart-container">
                    <canvas id="caLineChart" width="800" height="350"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Stock faible table --}}
    <div class="row mt-4">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-header redoff d-flex align-items-center gap-2">
                    <i class="fas fa-exclamation-circle"></i>
                    <h4 class="card-title mb-0">Produits en stock faible</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>N°</th>
                                    <th>Nom produit</th>
                                    <th>Stock actuel</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($produitsStockFaible as $produit)
                                    <tr class="low-stock-row">
                                        <td>{{ $loop->iteration }}</td>
                                        <td><strong>{{ $produit->nom }}</strong></td>
                                        <td><span class="badge bg-danger">{{ $produit->stock_actuel }}</span></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent sales --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header card-heade d-flex align-items-center gap-2">
                    <i class="fas fa-shopping-cart"></i>
                    <h4 class="card-title mb-0">Ventes récentes</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-centered table-striped table-nowrap mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Code reçu</th>
                                    <th>Client</th>
                                    <th>Paiement</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventes as $vente)
                                    <tr>
                                        <td class="fw-semibold">{{ $vente->code_recu }}</td>
                                        <td>{{ $vente->client->nom }}</td>
                                        <td>{{ $vente->mode_paiement }}</td>
                                        <td>{{ $vente->date_vente }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Recent clients --}}
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header card-heade d-flex align-items-center gap-2">
                    <i class="fas fa-users"></i>
                    <h4 class="card-title mb-0">Clients récents</h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-centered table-nowrap mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>Nom</th>
                                    <th>Adresse</th>
                                    <th>Téléphone</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($derniersClients as $client)
                                    <tr>
                                        <td class="fw-semibold">{{ $client->client->nom }}</td>
                                        <td>{{ $client->client->adresse }}</td>
                                        <td>{{ $client->client->telephone }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const labels = @json($labels);
    const data = @json($data);
    const data1 = @json($data1);
    const reste = @json($dataReste);

    const ctx = document.getElementById('caLineChart').getContext('2d');

    const caLineChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Chiffre d'affaires",
                    data: data,
                    backgroundColor: 'rgba(26, 35, 126, 0.85)',
                    borderColor: 'rgba(26, 35, 126, 1)',
                    borderRadius: 6,
                    borderWidth: 0,
                },
                {
                    label: 'Montant encaissé',
                    data: data1,
                    backgroundColor: 'rgba(22, 163, 74, 0.85)',
                    borderColor: 'rgba(22, 163, 74, 1)',
                    borderRadius: 6,
                    borderWidth: 0,
                },
                {
                    label: 'Reste à payer',
                    data: reste,
                    backgroundColor: 'rgba(220, 38, 38, 0.85)',
                    borderColor: 'rgba(220, 38, 38, 1)',
                    borderRadius: 6,
                    borderWidth: 0,
                }
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        pointStyle: 'rectRounded',
                        padding: 20,
                        font: { family: "'Inter', sans-serif", size: 12, weight: '500' }
                    }
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: "'Inter', sans-serif", size: 13, weight: '600' },
                    bodyFont: { family: "'Inter', sans-serif", size: 12 },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + new Intl.NumberFormat('fr-FR', { style: 'currency', currency: 'XOF' }).format(context.raw);
                        }
                    }
                }
            },
            interaction: { mode: 'nearest', intersect: false },
            scales: {
                x: {
                    display: true,
                    title: { display: true, text: 'Mois', font: { family: "'Inter', sans-serif", weight: '600' } },
                    grid: { display: false },
                    ticks: { font: { family: "'Inter', sans-serif", size: 11 } }
                },
                y: {
                    display: true,
                    title: { display: true, text: 'Montant', font: { family: "'Inter', sans-serif", weight: '600' } },
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.04)' },
                    ticks: {
                        font: { family: "'Inter', sans-serif", size: 11 },
                        callback: function(value) {
                            return new Intl.NumberFormat('fr-FR', { notation: 'compact' }).format(value);
                        }
                    }
                }
            }
        }
    });
</script>
@endsection
