#!/bin/bash

# Script pour tester le nouveau format d'email personnalisé
echo "Exécution de la commande stock:alert pour tester le nouveau format d'email..."
php artisan stock:alert
echo "Vérifiez l'interface MailHog à l'adresse http://localhost:8025 pour voir le nouveau format d'email."