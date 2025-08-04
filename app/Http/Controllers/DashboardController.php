<?php

namespace App\Http\Controllers;

use App\Models\Detail_vente;
use App\Models\Vente;
use Carbon\Carbon;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {  // Récupérer les données pour le graphique des ventes par mois
    // $chartData1 = DB::table('ventes')->where('statut', 'valide')
    //     ->select(
    //         DB::raw("strftime('%m', date_vente) as mois"),
    //         DB::raw("SUM(montant_total) as total")
    //     )
    //     ->groupBy(DB::raw("strftime('%m', date_vente)"))
    //     ->orderBy(DB::raw("strftime('%m', date_vente)"))
    //     ->get();

    // $labels = [];
    // $data = [];

    // foreach ($chartData1 as $row) {
    //     $labels[] = DateTime::createFromFormat('!m', $row->mois)->format('F');
    //     $data[] = (int) $row->total;
    // }

    // Récupérer l'utilisateur actuellement authentifié
    $user = Auth::user();

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

        // Afficher le dashboard correspondant au rôle de l'utilisateur
        if ($user->hasRole('admin')) {
            return view('dashboards.admin', compact(
                'ca_journalier',
                'chiffreAffaires',
                'nombreVentes',
                'derniersVentes',
                'derniersClients',
                // 'data',
                // 'labels',
                'chiffreAffaireMoisEnCours',
                'chartData' // Passer les données du graphique
            ));
        }

        if ($user->hasRole('vendeur')) {
            return view('dashboards.vendeur', compact('ventes'));
        }

    }
}
