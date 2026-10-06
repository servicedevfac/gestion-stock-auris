<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Paiement;
use App\Models\Produit;
use App\Models\Vente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Tableau de bord principal (Admin & Vendeur)
     */
    public function index()
    {
        $user = Auth::user();
        $driver = DB::getDriverName();

        // 1. Dashboard Administrateur / Super Admin
        if ($user->hasAnyRole(['Administrateur', 'super admin'])) {
            return $this->adminDashboard($driver);
        }

        // 2. Dashboard Commercial / Gestionnaire
        if ($user->hasAnyRole(['Gestionnaire', 'Commercial'])) {
            return $this->vendeurDashboard($user, $driver);
        }

        // 3. Fallback pour tout autre rôle
        return redirect()->route('ventes.index');
    }

    /**
     * Données du tableau de bord Administrateur
     */
    private function adminDashboard(string $driver)
    {
        $today = Carbon::today();
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Ventes globales
        $ventes = Vente::all();
        $derniersVentes = Vente::where('statut', 'valide')->orderByDesc('date_vente')->take(10)->get();

        // Clients récents
        $derniersClients = Vente::with('client')
            ->select('client_id')
            ->groupBy('client_id')
            ->orderByRaw('MAX(date_vente) DESC')
            ->take(10)
            ->get();

        // Chiffre d'affaires
        $ca_journalier = Paiement::whereDate('created_at', $today)->sum('montant');

        $chiffreAffaireMoisEnCours = Paiement::whereHas('vente', fn ($q) => $q->where('statut', 'valide'))
            ->whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('montant');

        $chiffreAffaires = Paiement::whereHas('vente', fn ($q) => $q->where('statut', 'valide'))
            ->whereYear('created_at', $currentYear)
            ->sum('montant');

        $chiffreAffairesGlobaux = Vente::where('statut', 'valide')->sum('montant_total');

        $totalEncaisse = Paiement::whereHas('vente', fn ($q) => $q->where('statut', 'valide'))->sum('montant');
        $totalVentesNonPayes = max(0, $chiffreAffairesGlobaux - $totalEncaisse);

        // Dépenses d'exploitation
        $depensesMois = Depense::whereYear('date_depense', $currentYear)
            ->whereMonth('date_depense', $currentMonth)
            ->sum('montant');

        $depensesAujourdhui = Depense::whereDate('date_depense', $today)->sum('montant');
        $depensesAnnuelles = Depense::whereYear('date_depense', $currentYear)->sum('montant');
        $beneficeNetMois = $chiffreAffaireMoisEnCours - $depensesMois;
        $derniersDepenses = Depense::with('user')->orderByDesc('date_depense')->take(5)->get();

        // Produits en alerte de stock
        $produitsStockFaible = Produit::select('produits.*')
            ->join('mouvement_stocks', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->selectRaw('
                SUM(CASE WHEN type_mouvement = \'entree\' THEN quantite ELSE 0 END) -
                SUM(CASE WHEN type_mouvement = \'sortie\' THEN quantite ELSE 0 END) as stock_actuel
            ')
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee', 'produits.created_at', 'produits.updated_at')
            ->havingRaw('(SUM(CASE WHEN type_mouvement = \'entree\' THEN quantite ELSE 0 END) - SUM(CASE WHEN type_mouvement = \'sortie\' THEN quantite ELSE 0 END)) <= seuil_alerte')
            ->get();

        // 12 derniers mois
        $months = collect(range(0, 11))
            ->map(fn ($i) => Carbon::now()->subMonths($i)->format('Y-m'))
            ->reverse()
            ->values();

        $labels = $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'))->values();

        $dateExprVente = match ($driver) {
            'sqlite' => "strftime('%Y-%m', date_vente)",
            'pgsql'  => "TO_CHAR(date_vente, 'YYYY-MM')",
            default  => "DATE_FORMAT(date_vente, '%Y-%m')",
        };

        $dateExprPaiement = match ($driver) {
            'sqlite' => "strftime('%Y-%m', created_at)",
            'pgsql'  => "TO_CHAR(created_at, 'YYYY-MM')",
            default  => "DATE_FORMAT(created_at, '%Y-%m')",
        };

        $dateExprDepense = match ($driver) {
            'sqlite' => "strftime('%Y-%m', date_depense)",
            'pgsql'  => "TO_CHAR(date_depense, 'YYYY-MM')",
            default  => "DATE_FORMAT(date_depense, '%Y-%m')",
        };

        $startMonthLimit = Carbon::now()->subMonths(11)->startOfMonth()->format('Y-m-d');

        // Ventes par mois
        $salesByMonth = Vente::selectRaw("$dateExprVente as month, SUM(montant_total) as total")
            ->where('date_vente', '>=', $startMonthLimit)
            ->where('statut', 'valide')
            ->groupBy('month')
            ->pluck('total', 'month');

        // Paiements par mois
        $paymentsByMonth = Paiement::whereHas('vente', fn ($q) => $q->where('statut', 'valide'))
            ->selectRaw("$dateExprPaiement as month, SUM(montant) as total")
            ->where('created_at', '>=', $startMonthLimit)
            ->groupBy('month')
            ->pluck('total', 'month');

        // Dépenses par mois
        $depensesByMonth = Depense::selectRaw("$dateExprDepense as month, SUM(montant) as total")
            ->where('date_depense', '>=', $startMonthLimit)
            ->groupBy('month')
            ->pluck('total', 'month');

        $data = $months->map(fn ($m) => (float) ($salesByMonth[$m] ?? 0))->values();
        $data1 = $months->map(fn ($m) => (float) ($paymentsByMonth[$m] ?? 0))->values();
        $dataDepenses = $months->map(fn ($m) => (float) ($depensesByMonth[$m] ?? 0))->values();
        $dataBenefice = $months->map(fn ($m) => (float) (($paymentsByMonth[$m] ?? 0) - ($depensesByMonth[$m] ?? 0)))->values();
        $dataReste = $months->map(fn ($m) => max(0, (float) (($salesByMonth[$m] ?? 0) - ($paymentsByMonth[$m] ?? 0))))->values();

        return view('dashboards.admin', compact(
            'ca_journalier',
            'chiffreAffaires',
            'totalVentesNonPayes',
            'ventes',
            'labels',
            'data',
            'dataReste',
            'data1',
            'dataDepenses',
            'dataBenefice',
            'chiffreAffairesGlobaux',
            'derniersVentes',
            'derniersClients',
            'produitsStockFaible',
            'chiffreAffaireMoisEnCours',
            'depensesMois',
            'depensesAujourdhui',
            'depensesAnnuelles',
            'beneficeNetMois',
            'derniersDepenses'
        ));
    }

    /**
     * Données du tableau de bord Vendeur / Commercial
     */
    private function vendeurDashboard($user, string $driver)
    {
        $userId = $user->id;
        $today = Carbon::today()->format('Y-m-d');
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $ventes = Vente::where('user_id', $userId)
            ->orderByDesc('date_vente')
            ->take(10)
            ->get();

        $chiffreAffairesvendeurs = DB::table('ventes')
            ->where('statut', 'valide')
            ->where('user_id', $userId)
            ->whereYear('date_vente', $currentYear)
            ->sum('montant_paye');

        $chiffreAffairesvendeursimpaye = DB::table('ventes')
            ->where('statut', 'valide')
            ->where('user_id', $userId)
            ->whereYear('date_vente', $currentYear)
            ->sum('reste_a_payer');

        $chiffreAffairesvendeursglobal = DB::table('ventes')
            ->where('statut', 'valide')
            ->where('user_id', $userId)
            ->sum('montant_total');

        $ca_journalier = DB::table('ventes')
            ->where('user_id', $userId)
            ->where('statut', 'valide')
            ->whereDate('date_vente', $today)
            ->sum('montant_paye');

        $ca_journalierNonPaye = DB::table('ventes')
            ->where('user_id', $userId)
            ->where('statut', 'valide')
            ->whereDate('date_vente', $today)
            ->sum('reste_a_payer');

        $chiffreAffaireMoisEnCours = DB::table('ventes')
            ->where('statut', 'valide')
            ->where('user_id', $userId)
            ->whereYear('date_vente', $currentYear)
            ->whereMonth('date_vente', $currentMonth)
            ->sum('montant_paye');

        $chiffreAffaireMoisEnCourNonPaye = DB::table('ventes')
            ->where('statut', 'valide')
            ->where('user_id', $userId)
            ->whereYear('date_vente', $currentYear)
            ->whereMonth('date_vente', $currentMonth)
            ->sum('reste_a_payer');

        $derniersClients = Vente::with('client')
            ->where('user_id', $userId)
            ->select('client_id')
            ->groupBy('client_id')
            ->orderByRaw('MAX(date_vente) DESC')
            ->take(10)
            ->get();

        $chiffreAffairesSemaine = DB::table('ventes')
            ->where('statut', 'valide')
            ->where('user_id', $userId)
            ->where('est_paye', true)
            ->whereBetween('date_vente', [Carbon::now()->startOfWeek()->format('Y-m-d'), Carbon::now()->endOfWeek()->format('Y-m-d')])
            ->sum('montant_total');

        $produitsStockFaible = Produit::select('produits.*')
            ->join('mouvement_stocks', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->selectRaw('
                SUM(CASE WHEN type_mouvement = \'entree\' THEN quantite ELSE 0 END) -
                SUM(CASE WHEN type_mouvement = \'sortie\' THEN quantite ELSE 0 END) as stock_actuel
            ')
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee', 'produits.created_at', 'produits.updated_at')
            ->havingRaw('(SUM(CASE WHEN type_mouvement = \'entree\' THEN quantite ELSE 0 END) - SUM(CASE WHEN type_mouvement = \'sortie\' THEN quantite ELSE 0 END)) <= seuil_alerte')
            ->get();

        // Graphiques
        $months = collect(range(0, 11))
            ->map(fn ($i) => now()->subMonths($i)->format('Y-m'))
            ->reverse()
            ->values();

        $labels = $months->map(fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'))->values();
        $startDate = now()->subMonths(11)->startOfMonth()->format('Y-m-d');

        $dateExprVente = match ($driver) {
            'sqlite' => "strftime('%Y-%m', date_vente)",
            'pgsql'  => "TO_CHAR(date_vente, 'YYYY-MM')",
            default  => "DATE_FORMAT(date_vente, '%Y-%m')",
        };

        $dateExprPaiement = match ($driver) {
            'sqlite' => "strftime('%Y-%m', date_paiement)",
            'pgsql'  => "TO_CHAR(date_paiement, 'YYYY-MM')",
            default  => "DATE_FORMAT(date_paiement, '%Y-%m')",
        };

        $sales = Vente::query()
            ->selectRaw("$dateExprVente as month, SUM(montant_total) as total")
            ->where('statut', 'valide')
            ->where('user_id', $userId)
            ->where('date_vente', '>=', $startDate)
            ->groupBy('month')
            ->pluck('total', 'month');

        $payments = Paiement::query()
            ->whereHas('vente', fn ($q) => $q->where('user_id', $userId)->where('statut', 'valide'))
            ->selectRaw("$dateExprPaiement as month, SUM(montant) as total")
            ->where('date_paiement', '>=', $startDate)
            ->groupBy('month')
            ->pluck('total', 'month');

        $data = $months->map(fn ($m) => (float) ($sales[$m] ?? 0))->values();
        $data1 = $months->map(fn ($m) => (float) ($payments[$m] ?? 0))->values();
        $dataReste = $months->map(fn ($m) => max(0, (float) (($sales[$m] ?? 0) - ($payments[$m] ?? 0))))->values();

        return view('dashboards.vendeur', compact(
            'chiffreAffairesvendeurs',
            'chiffreAffairesSemaine',
            'chiffreAffaireMoisEnCours',
            'ventes',
            'ca_journalierNonPaye',
            'chiffreAffaireMoisEnCourNonPaye',
            'data',
            'data1',
            'dataReste',
            'ca_journalier',
            'labels',
            'derniersClients',
            'produitsStockFaible',
            'chiffreAffairesvendeursglobal',
            'chiffreAffairesvendeursimpaye'
        ));
    }
}
