#!/bin/bash

# Script pour tester l'envoi d'email avec MailHog
echo "Exécution de la commande stock:alert pour tester l'envoi d'email..."
php artisan stock:alert
echo "Vérifiez l'interface MailHog à l'adresse http://localhost:8025 pour voir si l'email a été reçu."