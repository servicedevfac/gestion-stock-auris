<?php

namespace App\Http\Controllers;

use App\Models\Detail_vente;
use App\Models\Produit;
use App\Models\Vente;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DateTime;
use Illuminate\Http\Request;


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
                $derniersVentes = Vente::where('statut', 'valide')->orderBy('created_at', 'desc')->take(5)->get();

                // Définir l'expression de format de date selon le driver utilisé
                $dateExpression = $driver === 'sqlite'
                    ? "strftime('%Y-%m', ventes.created_at)"
                    : "DATE_FORMAT(ventes.created_at, '%Y-%m')";

                // Récupérer le total des ventes par produit et par mois
                $data = DB::table('detail_ventes')
                    ->join('ventes', 'detail_ventes.vente_id', '=', 'ventes.id')
                    ->join('produits', 'detail_ventes.produit_id', '=', 'produits.id')
                    ->select(
                        DB::raw("$dateExpression as mois"),
                        'produits.nom as produit',
                        DB::raw('SUM(detail_ventes.quantite * detail_ventes.prix) as total')
                    )
                    ->groupBy('mois', 'produit')
                    ->orderBy('mois')
                    ->get();

                // Transformer les données pour le graphique (tableau associatif mois/produit/total)
                $chartData = [];
                foreach ($data as $row) {
                    // Extraire le mois (YYYY-MM) et le convertir en nom de mois en français
                    $moisNom = Carbon::createFromFormat('Y-m', $row->mois)->locale('fr')->translatedFormat('F');
                    $chartData[$moisNom][$row->produit] = $row->total;
                }

                // Récupérer les 5 derniers clients ayant effectué une vente
                $derniersClients = Vente::with('client')->orderBy('created_at', 'desc')->select('client_id')->distinct()->take(5)->get();

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
                $chiffreAffaireParMois = DB::table('ventes')
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



            return view('dashboards.admin', compact(
                'ca_journalier',
                'chiffreAffaires',
                'nombreVentes',
                'derniersVentes',
                'derniersClients',
                'data',
                'produitsStockFaible',
                'chiffreAffaireMoisEnCours',
                'chartData' // Passer les données du graphique
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



        // Affichage par défaut si aucun rôle spécifique
        return view('dashboards.vendeur', compact('ventes'));
        $user =User:: create( [
            'nom' => 'John',
            'prenom' => 'Doe',
            'email' => 'admin@gamail.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        return view('dashboards.vendeur');
    }
}
