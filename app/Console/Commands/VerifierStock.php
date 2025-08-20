<?php

namespace App\Console\Commands;


use Illuminate\Console\Command;
use App\Models\Produit;
use App\Models\User;
use App\Notifications\StockAlerte;
use Illuminate\Support\Facades\Notification;

class VerifierStock extends Command
{
    protected $signature = 'stock:alert';
    protected $description = 'Envoie une alerte si le stock des produits est inférieur au seuil';

    public function handle()
    {
        $this->info('Vérification des stocks...');

        // Récupérer tous les admins ou utilisateurs à notifier
        $admins = User::role('admin')->get(); // ou User::where('role', 'admin')->get();

        // Récupérer tous les produits dont l'alerte n'a pas été envoyée
      $produits = Produit::all(); // récupérer tous les produits

            foreach ($produits as $produit) {
                if ($produit->stock_actuel <= $produit->seuil_alerte && $produit->alerte_envoyee == false) {
                    Notification::sendNow($admins, new StockAlerte($produit));

                    $produit->alerte_envoyee = true;
                    $produit->last_alerted_at = now();
                    $produit->save();

                    echo "Alerte envoyée pour : {$produit->nom}\n";
                } else {
                    // Si le stock est suffisant, on peut réinitialiser l'alerte
                    $produit->alerte_envoyee = false;
                    $produit->last_alerted_at = null;
                    $produit->save();
                    echo "Stock OK pour : {$produit->nom}\n";
                }
            }



        $this->info('Vérification terminée.');
    }
}
