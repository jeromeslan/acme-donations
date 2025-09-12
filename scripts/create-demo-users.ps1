# Script pour créer les utilisateurs de démonstration
# Usage: .\scripts\create-demo-users.ps1

Write-Host "Création des utilisateurs de démonstration..." -ForegroundColor Green

# Exécuter le seeder UserSeeder
docker-compose exec api-php php artisan db:seed --class=UserSeeder

Write-Host "Utilisateurs créés avec succès !" -ForegroundColor Green
Write-Host "Comptes disponibles :" -ForegroundColor Cyan
Write-Host "- Admin: admin@acme.test / password" -ForegroundColor Yellow
Write-Host "- User: user@acme.test / password" -ForegroundColor Yellow
Write-Host "- Creator: creator@acme.test / password" -ForegroundColor Yellow
Write-Host ""
Write-Host "Vous pouvez maintenant vous connecter sur :" -ForegroundColor Cyan
Write-Host "Frontend: http://localhost:5173" -ForegroundColor White
