#!/bin/bash

# Script pour tester les liens corrigés dans l'email
echo "Exécution de la commande stock:alert pour tester les liens corrigés..."
php artisan stock:alert
echo "Vérifiez l'interface MailHog à l'adresse http://localhost:8025 pour voir les emails avec les liens corrigés."
echo "Les liens devraient maintenant pointer vers http://localhost:8000/produits/ID au lieu de http://localhost/produits/ID."