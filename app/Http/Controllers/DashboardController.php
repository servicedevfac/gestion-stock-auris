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
                $derniersVentes = Vente::where('statut', 'valide')->orderBy('created_at', 'desc')->take(10)->get();

                // Définir l'expression de format de date selon le driver utilisé
                $dateExpression = $driver === 'sqlite'
                    ? "strftime('%Y-%m', ventes.created_at)"
                    : "DATE_FORMAT(ventes.created_at, '%Y-%m')";

                // Récupérer les 5 derniers clients ayant effectué une vente
                $derniersClients = Vente::with('client')->orderBy('created_at', 'desc')->select('client_id')->distinct()->take(10)->get();

                // Calculer le chiffre d'affaires du jour
                $ca_journalier = Paiement::whereDate('created_at', Carbon::today())->sum('montant');
                $ca_journalierNonPaye = DB::table('ventes')
                ->where('user_id', $user->id)
                ->where('statut', 'valide')
                ->whereDate('created_at', Carbon::today())
                ->sum('montant_total')-$ca_journalier;

               $chiffreAffaireMoisEnCours = Paiement::whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('montant');

                // Calculer le chiffre d'affaires total
                $chiffreAffaires = Paiement::whereYear('created_at', Carbon::now()->year)->sum('montant');
                $chiffreAffairesGlobaux = Vente::whereYear('created_at', Carbon::now()->year)->sum('montant_total');
                $totalVentesNonPayes = $chiffreAffairesGlobaux - Paiement::sum('montant');

                $produitsStockFaible = Produit::select('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee','produits.created_at', 'produits.updated_at')
            ->join('mouvement_stocks', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->selectRaw('
                SUM(CASE WHEN type_mouvement = "entree" THEN quantite ELSE 0 END) -
                SUM(CASE WHEN type_mouvement = "sortie" THEN quantite ELSE 0 END) as stock_actuel
            ')
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee','produits.created_at', 'produits.updated_at')
            ->havingRaw('stock_actuel <= seuil_alerte')
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
        ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->where('statut', 'valide')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    // paiements par mois
    $sales1 = Paiement::selectRaw("$dateExpr as month, SUM(montant) as total")
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
        ($sale->has($m) ? (float) $sale[$m]->total : 0) -
        ($sales1->has($m) ? (float) $sales1[$m]->total : 0));
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
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get();
                // Récupérer le chiffre d'affaires du vendeur
            $chiffreAffairesvendeurs = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)->sum('montant_paye');
            $chiffreAffairesvendeursimpaye = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)->sum('reste_a_payer');
            $chiffreAffairesvendeursglobal = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->sum('montant_total');
            $ca_journalier = DB::table('ventes')
             ->where('user_id', $user->id)
             ->where('statut', 'valide')
             ->whereDate('created_at', Carbon::today())
             ->sum('montant_paye');
            $ca_journalierNonPaye = DB::table('ventes')
             ->where('user_id', $user->id)
             ->where('statut', 'valide')
             ->whereDate('created_at', Carbon::today())
             ->sum('reste_a_payer');

                // Récupérer le chiffre d'affaires du mois en cours pour le vendeur
            $chiffreAffaireMoisEnCours = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('montant_paye');
            $chiffreAffaireMoisEnCourNonPaye = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('reste_a_payer');


        // Récupérer les 5 derniers clients ayant effectué une vente pour ce vendeur
            $derniersClients = Vente::with('client')
                ->where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->select('client_id')
                ->distinct()
                ->take(10)
                ->get();
            // Récupérer le nombre total de ventes pour ce vendeur

            $chiffreAffairesSemaine = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->where('est_paye', true)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum('montant_total');
            // Récupérer les données pour les produits dont le stock est faible

            $produitsStockFaible = Produit::select('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee','produits.created_at', 'produits.updated_at')
            ->join('mouvement_stocks', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->selectRaw('
                SUM(CASE WHEN type_mouvement = "entree" THEN quantite ELSE 0 END) -
                SUM(CASE WHEN type_mouvement = "sortie" THEN quantite ELSE 0 END) as stock_actuel
            ')
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.created_at', 'produits.updated_at')
            ->havingRaw('stock_actuel <= seuil_alerte')
            ->get();
            $months = collect(range(0, 11))
        ->map(fn($i) => Carbon::now()->subMonths($i)->format('Y-m'))
        ->reverse();
          $labels = $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));

              $driver = DB::getDriverName();
    $dateExpr = match ($driver) {
        'mysql'  => "DATE_FORMAT(created_at, '%Y-%m')",
        'sqlite' => "strftime('%Y-%m', created_at)",
        'pgsql'  => "TO_CHAR(created_at, 'YYYY-MM')",
        default  => "DATE_FORMAT(created_at, '%Y-%m')",
    };

    // ventes par mois
    $sale = Vente::selectRaw("$dateExpr as month, SUM(montant_total) as total")
        ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->where('statut', 'valide')
         ->where('user_id', $user->id)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    // paiements par mois
  $userId = $user->id;

$sales1 = Paiement::whereHas('vente', function ($q) use ($userId) {
        $q->where('user_id', $userId);
    })
    ->selectRaw("$dateExpr as month, SUM(montant) as total")
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
        ($sale->has($m) ? (float) $sale[$m]->total : 0) -
        ($sales1->has($m) ? (float) $sales1[$m]->total : 0));

        $labels = $labels->values();
        $data   = $data->values();
        $data1  = $data1->values();
        $dataReste = $dataReste->values();


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
