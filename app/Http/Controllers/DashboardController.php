<?php

namespace App\Http\Controllers;


use App\Models\Paiement;
use App\Models\Produit;
use App\Models\Vente;
use App\Models\Detail_vente;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {  // Récupérer les données pour le graphique des ventes par mois

        $user = Auth::user();
        // Afficher le dashboard correspondant au rôle de l'utilisateur
        if ($user->hasRole('Administrateur') || $user->hasRole('super admin')) {
            // Récupérer l'utilisateur actuellement authentifié
                // Récupérer toutes les ventes
                $ventes = Vente::all();

                // Récupérer le nom du driver de la base de données (sqlite, mysql, etc.)
                $driver = DB::getDriverName();

                // Récupérer les 5 dernières ventes (par date de création décroissante)
                $derniersVentes = Vente::where('statut', 'valide')->orderBy('date_vente', 'desc')->take(10)->get();

                // Définir l'expression de format de date selon le driver utilisé
                $dateExpression = $driver === 'sqlite'
                    ? "strftime('%Y-%m', ventes.date_vente)"
                    : "DATE_FORMAT(ventes.date_vente, '%Y-%m')";

                // Récupérer les 5 derniers clients ayant effectué une vente vente
                $derniersClients = Vente::with('client')->select('client_id')->groupBy('client_id')->orderByRaw('MAX(date_vente) DESC')->take(10)->get();

                // Calculer le chiffre d'affaires du jour
                $ca_journalier = Paiement::whereDate('created_at', Carbon::today())->sum('montant');
                $ca_journalierNonPaye = DB::table('ventes')
                ->where('user_id', $user->id)
                ->where('statut', 'valide')
                ->whereDate('date_vente', Carbon::today())
                ->sum('montant_total')-$ca_journalier;
               $chiffreAffaireMoisEnCours = Paiement::whereHas('vente', fn($q) => $q->where('statut', 'valide'))->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('montant');

                                // Calculer le chiffre d'affaires total
                $chiffreAffaires = Paiement::whereHas('vente', fn($q) => $q->where('statut', 'valide'))
                    ->whereYear('created_at', Carbon::now()->year)
                    ->sum('montant');
                $chiffreAffairesGlobaux = Vente::where('statut', 'valide')->sum('montant_total');
                
                $totalVentesNonPayes = max( 0,$chiffreAffairesGlobaux - Paiement::whereHas('vente', fn($q) => $q->where('statut', 'valide'))->sum('montant'));

                $produitsStockFaible = Produit::select('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee','produits.created_at', 'produits.updated_at')
            ->join('mouvement_stocks', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->selectRaw('
                SUM(CASE WHEN type_mouvement = \'entree\' THEN quantite ELSE 0 END) -
                SUM(CASE WHEN type_mouvement = \'sortie\' THEN quantite ELSE 0 END) as stock_actuel
            ')
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee','produits.created_at', 'produits.updated_at')
            ->havingRaw('(SUM(CASE WHEN type_mouvement = \'entree\' THEN quantite ELSE 0 END) - SUM(CASE WHEN type_mouvement = \'sortie\' THEN quantite ELSE 0 END)) <= seuil_alerte')
            ->get();

    //
    $months = collect(range(0, 11))
        ->map(fn($i) => Carbon::now()->subMonths($i)->format('Y-m'))
        ->reverse();

    // labels lisibles
    $labels = $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));

    // détecter le driver (MySQL, PostgreSQL, SQLite)
    $driver = DB::getDriverName();
    $dateExpr = match ($driver) {
        'mysql'  => "DATE_FORMAT(created_at, '%Y-%m')",
        'sqlite' => "strftime('%Y-%m', created_at)",
        'pgsql'  => "TO_CHAR(created_at, 'YYYY-MM')",
        default  => "DATE_FORMAT(created_at, '%Y-%m')",
    };

    // ventes par mois
    $sale = Vente::selectRaw("$dateExpr as month, SUM(montant_total) as total")
        ->where('date_vente', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->where('statut', 'valide')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    // paiements par mois
    $sales1 = Paiement::whereHas('vente', fn($q) => $q->where('statut', 'valide'))->selectRaw("$dateExpr as month, SUM(montant) as total")
        ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    // données ventes par mois
    $data = $months->map(fn($m) => $sale->has($m) ? (float) $sale[$m]->total : 0);

    // données paiements par mois
    $data1 = $months->map(fn($m) => $sales1->has($m) ? (float) $sales1[$m]->total : 0);

 // reste à encaisser par mois
$dataReste = $months->map(fn($m) => 
    max(
        0,
        ($sale->has($m) ? (float) $sale[$m]->total : 0) -
        ($sales1->has($m) ? (float) $sales1[$m]->total : 0)
    )
);

        $labels = $labels->values();
        $data   = $data->values();
        $data1  = $data1->values();
        $dataReste = $dataReste->values();



    $totalVentesPayes = $ventes->where('est_paye', true)->sum('montant_total');
//dd($labels, $data);
            return view('dashboards.admin', compact(
                'ca_journalier',
                'chiffreAffaires',
                'totalVentesNonPayes',
                'ventes',
                'labels',
                'data',
                'dataReste',
                'data1',
                'chiffreAffairesGlobaux',
                'derniersVentes',
                'derniersClients',
                'produitsStockFaible',
                'chiffreAffaireMoisEnCours',
                //'chartData' // Passer les données du graphique
            ));
        }

        if ($user->hasRole('Gestionnaire')||$user->hasRole('Commercial')) {
            // Récupérer les 10 dernières ventes du vendeur
            $ventes = Vente::where('user_id', $user->id)
                ->orderBy('date_vente', 'desc')
                ->take(10)
                ->get();
                // Récupérer le chiffre d'affaires du vendeur
            $chiffreAffairesvendeurs = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('date_vente', Carbon::now()->year)->sum('montant_paye');
            $chiffreAffairesvendeursimpaye = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('date_vente', Carbon::now()->year)->sum('reste_a_payer');
            $chiffreAffairesvendeursglobal = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->sum('montant_total');
            $ca_journalier = DB::table('ventes')
             ->where('user_id', $user->id)
             ->where('statut', 'valide')
             ->whereDate('date_vente', Carbon::today())
             ->sum('montant_paye');
            $ca_journalierNonPaye = DB::table('ventes')
             ->where('user_id', $user->id)
             ->where('statut', 'valide')
             ->whereDate('date_vente', Carbon::today())
             ->sum('reste_a_payer');

                // Récupérer le chiffre d'affaires du mois en cours pour le vendeur
            $chiffreAffaireMoisEnCours = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('date_vente', Carbon::now()->year)
                ->whereMonth('date_vente', Carbon::now()->month)
                ->sum('montant_paye');
            $chiffreAffaireMoisEnCourNonPaye = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('date_vente', Carbon::now()->year)
                ->whereMonth('date_vente', Carbon::now()->month)
                ->sum('reste_a_payer');


        // Récupérer les 5 derniers clients ayant effectué une vente pour ce vendeur
            $derniersClients = Vente::with('client')
                ->where('user_id', $user->id)
                ->select('client_id')
                ->groupBy('client_id')
                ->orderByRaw('MAX(date_vente) DESC')
                ->take(10)
                ->get();
            // Récupérer le nombre total de ventes pour ce vendeur

            $chiffreAffairesSemaine = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->where('est_paye', true)
                ->whereBetween('date_vente', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum('montant_total');
            // Récupérer les données pour les produits dont le stock est faible

              $produitsStockFaible = Produit::select('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee','produits.created_at', 'produits.updated_at')
            ->join('mouvement_stocks', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->selectRaw('
                SUM(CASE WHEN type_mouvement = \'entree\' THEN quantite ELSE 0 END) -
                SUM(CASE WHEN type_mouvement = \'sortie\' THEN quantite ELSE 0 END) as stock_actuel
            ')
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee','produits.created_at', 'produits.updated_at')
            ->havingRaw('(SUM(CASE WHEN type_mouvement = \'entree\' THEN quantite ELSE 0 END) - SUM(CASE WHEN type_mouvement = \'sortie\' THEN quantite ELSE 0 END)) <= seuil_alerte')
            ->get();


$userId = $user->id;

//////////////////////////////////////////////////
// 1️⃣ Générer les 12 derniers mois
//////////////////////////////////////////////////

$months = collect(range(0, 11))
    ->map(fn ($i) => now()->subMonths($i)->format('Y-m'))
    ->reverse()
    ->values();

$labels = $months->map(
    fn ($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y')
);

$startDate = now()->subMonths(11)->startOfMonth()->format('Y-m-d');

//////////////////////////////////////////////////
// 2️⃣ EXPRESSIONS MYSQL (string ➜ date)
//////////////////////////////////////////////////

$dateExprVente = "
    DATE_FORMAT(
        STR_TO_DATE(date_vente, '%Y-%m-%d'),
        '%Y-%m'
    )
";

$dateExprPaiement = "
    DATE_FORMAT(
        STR_TO_DATE(date_paiement, '%Y-%m-%d'),
        '%Y-%m'
    )
";

//////////////////////////////////////////////////
// 3️⃣ VENTES PAR MOIS
//////////////////////////////////////////////////

$sales = Vente::query()
    ->selectRaw("$dateExprVente as month, SUM(montant_total) as total")
    ->where('statut', 'valide')
    ->where('user_id', $userId)
    ->whereRaw("STR_TO_DATE(date_vente, '%Y-%m-%d') >= ?", [$startDate])
    ->groupBy('month')
    ->pluck('total', 'month');

//////////////////////////////////////////////////
// 4️⃣ PAIEMENTS PAR MOIS
//////////////////////////////////////////////////

$payments = Paiement::query()
    ->whereHas('vente', fn ($q) =>
        $q->where('user_id', $userId)
          ->where('statut', 'valide')
    )
    ->selectRaw("$dateExprPaiement as month, SUM(montant) as total")
    ->whereRaw("STR_TO_DATE(date_paiement, '%Y-%m-%d') >= ?", [$startDate])
    ->groupBy('month')
    ->pluck('total', 'month');

//////////////////////////////////////////////////
// 5️⃣ DATASETS
//////////////////////////////////////////////////

$data = $months->map(fn ($m) => (float) ($sales[$m] ?? 0));
$data1 = $months->map(fn ($m) => (float) ($payments[$m] ?? 0));

$dataReste = $months->map(fn ($m) =>
    max(0, ($sales[$m] ?? 0) - ($payments[$m] ?? 0))
);

//////////////////////////////////////////////////
// 6️⃣ RESULTAT FINAL (Chart.js ready)
//////////////////////////////////////////////////




            return view('dashboards.vendeur',
            compact(
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
}
