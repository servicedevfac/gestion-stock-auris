<?php

namespace App\Http\Controllers;


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
                $ca_journalier = DB::table('ventes')->where('est_paye', true)->whereDate('created_at', Carbon::today())->sum('montant_total');

                // Calculer le chiffre d'affaires du mois en cours
                $chiffreAffaireMoisEnCours = DB::table('ventes')->where('statut', 'valide')->where('est_paye', true)
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->sum('montant_total');

                // Calculer le chiffre d'affaires total
                $chiffreAffaires = Vente::where('statut', 'valide')->where('est_paye', true)->whereYear('created_at', Carbon::now()->year)->sum('montant_total');
                $totalVentesNonPayes = Vente::where('statut', 'valide')->where('est_paye', false)->whereYear('created_at', Carbon::now()->year)->sum('montant_total');

                // Compter le nombre total de ventes
                $nombreVentes = Vente::where('statut', 'valide')->count();

                // Récupérer le chiffre d'affaires groupé par mois
                $chiffreAffaireParMois = DB::table('ventes')->where('statut', 'valide')
                    ->select(
                        DB::raw($driver === 'sqlite'
                            ? "strftime('%Y-%m', created_at) as mois"
                            : "DATE_FORMAT(created_at, '%Y-%m') as mois"
                        ),
                        DB::raw('SUM(montant_total) as total')
                    )
                    ->groupBy('mois')
                    ->orderBy('mois')
                    ->get();

                $produitsStockFaible = Produit::select('produits.*')
            ->join('mouvement_stocks', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->selectRaw('
                SUM(CASE WHEN type_mouvement = "entree" THEN quantite ELSE 0 END) -
                SUM(CASE WHEN type_mouvement = "sortie" THEN quantite ELSE 0 END) as stock_actuel
            ')
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.alerte_envoyee','produits.created_at', 'produits.updated_at')
            ->havingRaw('stock_actuel <= seuil_alerte')
            ->get();

            $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->format('Y-m'));
        }

       $driver = DB::getDriverName();

    $dateExpr = match ($driver) {
        'mysql'  => "DATE_FORMAT(created_at, '%Y-%m')",
        'sqlite' => "strftime('%Y-%m', created_at)",
        'pgsql'  => "TO_CHAR(created_at, 'YYYY-MM')",
        default  => "DATE_FORMAT(created_at, '%Y-%m')",
    };

    $sales = Vente::selectRaw("$dateExpr as month, SUM(montant_total) as total")
        ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->where('statut', 'valide')
        ->where('est_paye',false)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');
    $sales1 = Vente::selectRaw("$dateExpr as month, SUM(montant_total) as total")
        ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->where('statut', 'valide')
        ->where('est_paye', true)
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    // génère les 12 derniers mois
    $months = collect(range(0, 11))
        ->map(fn($i) => Carbon::now()->subMonths($i)->format('Y-m'))
        ->reverse();

    // labels lisibles
    $labels = $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));

    // données
    $data = $months->map(fn($m) => $sales->has($m) ? (float) $sales[$m]->total : 0);
    $data1 = $months->map(fn($m) => $sales1->has($m) ? (float) $sales1[$m]->total : 0);



    // réindexe pour JS
    $labels = $labels->values();
    $data = $data->values();
    $data1 = $data1->values();
    $totalVentesPayes = $ventes->where('est_paye', true)->sum('montant_total');
//dd($labels, $data);

            return view('dashboards.admin', compact(
                'ca_journalier',
                'chiffreAffaires',
                'nombreVentes',
                'totalVentesNonPayes',
                'ventes',
                'labels',
                'data',
                'data1',
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
                ->sum('montant_total');
                // Récupérer le chiffre d'affaires du mois en cours pour le vendeur
            $chiffreAffaireMoisEnCours = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->sum('montant_total');
        // Récupérer les 5 derniers clients ayant effectué une vente pour ce vendeur
        $derniersClients = Vente::with('client')
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->select('client_id')
            ->distinct()
            ->take(10)
            ->get();
            // Récupérer le nombre total de ventes pour ce vendeur
            $nombreVentes = Vente::where('statut', 'valide')
                ->where('user_id', $user->id)
                ->count();
            $chiffreAffairesSemaine = DB::table('ventes')
                ->where('statut', 'valide')
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])
                ->sum('montant_total');
            // Récupérer les données pour les produits dont le stock est faible


            $produitsStockFaible = Produit::select('produits.*')
            ->join('mouvement_stocks', 'produits.id', '=', 'mouvement_stocks.produit_id')
            ->selectRaw('
                SUM(CASE WHEN type_mouvement = "entree" THEN quantite ELSE 0 END) -
                SUM(CASE WHEN type_mouvement = "sortie" THEN quantite ELSE 0 END) as stock_actuel
            ')
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.created_at', 'produits.updated_at')
            ->havingRaw('stock_actuel <= seuil_alerte')
            ->get();

              $driver = DB::getDriverName();

    $dateExpr = match ($driver) {
        'mysql'  => "DATE_FORMAT(created_at, '%Y-%m')",
        'sqlite' => "strftime('%Y-%m', created_at)",
        'pgsql'  => "TO_CHAR(created_at, 'YYYY-MM')",
        default  => "DATE_FORMAT(created_at, '%Y-%m')",
    };

        $sales = Vente::selectRaw("$dateExpr as month, SUM(montant_total) as total")
        ->where('user_id', $user->id)
        ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
        ->where('statut', 'valide')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->keyBy('month');

    // génère les 12 derniers mois
    $months = collect(range(0, 11))
        ->map(fn($i) => Carbon::now()->subMonths($i)->format('Y-m'))
        ->reverse();

    // labels lisibles
    $labels = $months->map(fn($m) => Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y'));

    // données
    $data = $months->map(fn($m) => $sales->has($m) ? (float) $sales[$m]->total : 0);

    // réindexe pour JS
    $labels = $labels->values();
    $data = $data->values();

    $totalVentesPayes = $ventes->where('est_paye', true)->sum('montant_total');

            return view('dashboards.vendeur',
            compact(
                'chiffreAffairesvendeurs',
                'chiffreAffairesSemaine',
                        'chiffreAffaireMoisEnCours',
                        'nombreVentes',
                        'ventes',
                        'data',
                        'labels',
                        'derniersClients',
                        'produitsStockFaible',
                        'totalVentesPayes'
            ));
        }



    }
}
