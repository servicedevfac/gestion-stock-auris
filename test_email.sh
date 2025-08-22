#!/bin/bash

# Chemin vers le projet
PROJECT_PATH="/Volumes/Sans titre/app/gestion-stock-auris"

# Chemin vers PHP
PHP_PATH="/Users/palmer/.config/herd-lite/bin/php"

# Se déplacer dans le répertoire du projet
cd "$PROJECT_PATH"

# Exécuter la commande stock:alert pour tester l'envoi d'emails
echo "Exécution de la commande stock:alert pour tester l'envoi d'emails..."
"$PHP_PATH" artisan stock:alert

echo "Terminé! Vérifiez votre boîte de réception ou Mailtrap pour voir si l'email a été envoyé."
echo "Si vous utilisez Mailtrap, connectez-vous à votre compte pour voir les emails reçus."
echo "URL: https://mailtrap.io/"