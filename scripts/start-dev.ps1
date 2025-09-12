# Script de démarrage robuste pour éviter les 502 Bad Gateway
# Usage: .\scripts\start-dev.ps1

Write-Host "Démarrage de l'environnement de développement..." -ForegroundColor Green

# Arrêter tous les services existants
Write-Host "Arrêt des services existants..." -ForegroundColor Yellow
docker-compose down

# Démarrer les services dans l'ordre
Write-Host "Démarrage des services..." -ForegroundColor Yellow
docker-compose up -d redis
Start-Sleep -Seconds 2

docker-compose up -d api-php
Start-Sleep -Seconds 5

docker-compose up -d api-nginx
Start-Sleep -Seconds 2

docker-compose up -d queue-worker
Start-Sleep -Seconds 2

docker-compose up -d web

# Attendre que tous les services soient prêts
Write-Host "Attente que tous les services soient prêts..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Corriger les permissions de la base de données SQLite
Write-Host "Correction des permissions de la base de données..." -ForegroundColor Yellow
docker-compose exec api-php chmod 666 database/database.sqlite

# Vérifier l'état des services
Write-Host "Vérification de l'état des services..." -ForegroundColor Yellow
docker-compose ps

# Tester l'API
Write-Host "Test de l'API..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080/api/stats" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Host "API fonctionne correctement !" -ForegroundColor Green
    } else {
        Write-Host "API retourne un code d'erreur: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "Erreur lors du test de l'API: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Redémarrage des services API..." -ForegroundColor Yellow
    docker-compose restart api-php api-nginx
    Start-Sleep -Seconds 5
    
    # Nouveau test
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8080/api/stats" -UseBasicParsing -TimeoutSec 10
        if ($response.StatusCode -eq 200) {
            Write-Host "API fonctionne après redémarrage !" -ForegroundColor Green
        }
    } catch {
        Write-Host "L'API ne fonctionne toujours pas. Vérifiez les logs avec: docker-compose logs api-php" -ForegroundColor Red
    }
}

Write-Host "Environnement de développement prêt !" -ForegroundColor Green
Write-Host "Frontend: http://localhost:5173" -ForegroundColor Cyan
Write-Host "API: http://localhost:8080" -ForegroundColor Cyan
Write-Host "Redis: localhost:6379" -ForegroundColor Cyan
