<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\Vente;
use App\Models\Paiement;

class paiementController extends Controller
{
    public function store(Request $request, $venteId)
    {
    $vente = Vente::findOrFail($venteId);

    // Calcul du nouveau reste à payer
    $nouveauMontantPaye = $vente->montant_paye + $request->montant;
    $resteAPayer = max($vente->montant_total - $nouveauMontantPaye, 0);

    // Ajouter un paiement (avance ou règlement)
    $paiement = $vente->paiements()->create([
        'montant'       => $request->montant,
        'mode_paiement' => $request->mode_paiement,
        'reste_a_payer' => $resteAPayer, // ✅ stocké aussi dans paiements
    ]);

    // Mettre à jour la vente
    $vente->montant_paye = $nouveauMontantPaye;
    $vente->reste_a_payer = $resteAPayer;
    $vente->est_paye = ($resteAPayer <= 0);
    $vente->save();

    return back()->with(
        'success',
        "Paiement enregistré : {$paiement->montant} FCFA. Reste à payer : {$vente->reste_a_payer} FCFA."
    );

}
function ticketpaiement($id)
{
    $paiement = Paiement::with('vente')->findOrFail($id);
    
    $pdf = PDF::loadView('admin.ticket_de_paiment.ticket_paiement', compact('paiement'))
        ->setPaper([0, 0, 360.77, 600], 'portrait');

    $date = now()->format('y_m_d'); // Exemple : 25_09_18
    $numero = str_pad($paiement->id, 3, '0', STR_PAD_LEFT);


    $filename = 'paiement_' . $date . '_' . $numero . '.pdf';

    return $pdf->stream($filename);
}
}
