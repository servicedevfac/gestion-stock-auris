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
        if ($user->hasRole('admin') || $user->hasRole('super admin')) {
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
                $ca_journalier = DB::table('ventes')->whereDate('created_at', Carbon::today())->sum('montant_total');

                // Calculer le chiffre d'affaires du mois en cours
                $chiffreAffaireMoisEnCours = DB::table('ventes')->where('statut', 'valide')
                    ->whereYear('created_at', Carbon::now()->year)
                    ->whereMonth('created_at', Carbon::now()->month)
                    ->sum('montant_total');

                // Calculer le chiffre d'affaires total
                $chiffreAffaires = Vente::where('statut', 'valide')->sum('montant_total');

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
            ->groupBy('produits.id', 'produits.nom', 'produits.prix', 'produits.seuil_alerte', 'produits.created_at', 'produits.updated_at')
            ->havingRaw('stock_actuel <= seuil_alerte')
            ->get();
            $months = collect();
        for ($i = 11; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->format('Y-m'));
        }

        // Récupère le CA groupé par mois (assume champ `total` et `created_at`)
        $sales = Vente::selectRaw("strftime('%Y-%m', created_at) as month, SUM(montant_total) as total")
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->where('statut', 'valide')
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->keyBy('month');

        // Mappe sur les 12 mois en mettant 0 si absent
        $labels = $months->map(function($m){
            // option : rendre plus lisible → '2025-08' -> 'Août 2025'
            return Carbon::createFromFormat('Y-m', $m)->translatedFormat('M Y');
        });

        $data = $months->map(function($m) use ($sales){
            return $sales->has($m) ? (float) $sales[$m]->total : 0;
        });

            return view('dashboards.admin', compact(
                'ca_journalier',
                'chiffreAffaires',
                'nombreVentes',
                'ventes',
                'labels',
                'data',
                'derniersVentes',
                'derniersClients',
                'produitsStockFaible',
                'chiffreAffaireMoisEnCours',
                //'chartData' // Passer les données du graphique
            ));
        }


        if ($user->hasRole('vendeur')) {
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
            // ca de la semaine
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

            foreach ($produitsStockFaible as $produit) {
                if ($produit->alerte_envoyee) {
                    // Réinitialiser l'alerte si le stock est reconstitué
                    $produit=Produit::find($produit->id);
                    $produit->update([
                        'alerte_envoyee' => false,
                        'last_alerted_at' => null
                    ]);
                }
            }


            return view('dashboards.vendeur',
            compact(
                'chiffreAffairesvendeurs',
                'chiffreAffairesSemaine',
                        'chiffreAffaireMoisEnCours',
                        'nombreVentes',
                        'ventes',
                        'derniersClients',
                        'produitsStockFaible'
            ));
        }

    }
}
