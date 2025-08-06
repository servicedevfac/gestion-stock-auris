<?php

namespace App\Http\Controllers;

use App\Models\Horaire;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\HistoriqueHoraire;

class HoraireController extends Controller
{
   public function index()
    {
       $horaires = Horaire::orderByRaw("
    CASE jour_semaine
        WHEN 'lundi' THEN 1
        WHEN 'mardi' THEN 2
        WHEN 'mercredi' THEN 3
        WHEN 'jeudi' THEN 4
        WHEN 'vendredi' THEN 5
        WHEN 'samedi' THEN 6
        WHEN 'dimanche' THEN 7
        ELSE 8
    END
")->get();

        return view('admin.horaires.index', compact('horaires'));
    }

    public function edit()
    {
        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];
        $horaires = Horaire::all()->keyBy('jour_semaine');

        return view('admin.horaires.edit', compact('jours', 'horaires'));
    }

    public function update(Request $request)
    {

        $jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi', 'samedi', 'dimanche'];

    foreach ($jours as $jour) {
        $ouverture = $request->input("heure_ouverture.$jour");
        $fermeture = $request->input("heure_fermeture.$jour");

        $horaireExistant = Horaire::where('jour_semaine', $jour)->first();

        // S'il y a une modification, on enregistre dans l'historique
        if ($horaireExistant &&
            ($horaireExistant->heure_ouverture != $ouverture || $horaireExistant->heure_fermeture != $fermeture)
        ) {
            HistoriqueHoraire::create([
                'jour_semaine' => $jour,
                'ancienne_ouverture' => $horaireExistant->heure_ouverture,
                'ancienne_fermeture' => $horaireExistant->heure_fermeture,
                'nouvelle_ouverture' => $ouverture,
                'nouvelle_fermeture' => $fermeture,
                'id_utilisateur' => Auth::id(),
            ]);
        }

        Horaire::updateOrCreate(
            ['jour_semaine' => $jour],
            [
                'heure_ouverture' => $ouverture,
                'heure_fermeture' => $fermeture,
                'id_utilisateur' => Auth::id(),
            ]
        );
    }

    return redirect()->route('admin.horaires.index')->with('success', 'Horaires mis à jour');
}

public function historique()
{
    $historique = HistoriqueHoraire::with('user')->latest()->paginate(20); // pagination

    return view('admin.horaires.historique', compact('historique'));
}

}
