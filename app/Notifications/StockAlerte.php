<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StockAlerte extends Notification implements ShouldQueue
{
    use Queueable;

    protected $produits;

    public function __construct($produits)
    {
        $this->produits = $produits;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $message = (new MailMessage)
            ->subject('⚠️ Alerte Stock Faible')
            ->greeting('Attention!')
            ->line('Les produits suivants ont atteint un niveau de stock critique :');

        // Si c'est un tableau d'items (format du VenteController)
        if (is_array($this->produits)) {
            foreach ($this->produits as $item) {
                $message->line("**{$item['nom']}** - Stock actuel: {$item['stock']} - Seuil d'alerte: {$item['seuil']}");
                
                // Si l'URL est fournie
                if (isset($item['url'])) {
                    $message->action("Voir {$item['nom']}", $item['url']);
                }
            }
        } 
        // Si c'est un objet produit unique (ancien format)
        else {
            $message->line("Le produit **{$this->produits->nom}** a atteint un stock critique.")
                ->line("Stock actuel : {$this->produits->stock_actuel}")
                ->line("Seuil d'alerte : {$this->produits->seuil_alerte}")
                ->action('Voir le stock', url('/produits/' . $this->produits->id));
        }
        
        return $message->line('Veuillez réapprovisionner rapidement.');
    }
}
