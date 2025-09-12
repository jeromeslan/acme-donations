#!/bin/bash
# Script pour créer les utilisateurs de démonstration
# Usage: ./scripts/create-demo-users.sh

echo "Création des utilisateurs de démonstration..."

# Exécuter le seeder UserSeeder
docker-compose exec api-php php artisan db:seed --class=UserSeeder

echo "Utilisateurs créés avec succès !"
echo "Comptes disponibles :"
echo "- Admin: admin@acme.test / password"
echo "- User: user@acme.test / password"
echo "- Creator: creator@acme.test / password"
echo ""
echo "Vous pouvez maintenant vous connecter sur :"
echo "Frontend: http://localhost:5173"
