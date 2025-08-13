<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Produit;
use App\Models\User;
use App\Notifications\StockAlerte;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\DB;

class VerifierStock extends Command
{
    protected $signature = 'stock:verifier';
    protected $description = 'Vérifie chaque produit et envoie une alerte si stock bas';

    public function handle()
    {
        $admin = User::where('is_admin', true)->first();
        Produit::where('stock', '<=', \DB::raw('seuil_alerte'))
            ->where('alerte_envoyee', false)
            ->chunk(50, function ($produits) use ($admin) {
                foreach ($produits as $produit) {
                    Notification::send($admin, new StockAlerte($produit));
                    $produit->alerte_envoyee = true;
                    $produit->save();
                }
            });

        $this->info('Vérification des stocks terminée.');
    }
}
