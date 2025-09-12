# Robust startup script to avoid 502 Bad Gateway errors
# Usage: .\scripts\start-dev.ps1

Write-Host "Starting development environment..." -ForegroundColor Green

# Stop all existing services
Write-Host "Stopping existing services..." -ForegroundColor Yellow
docker-compose down

# Start services in order
Write-Host "Starting services..." -ForegroundColor Yellow
docker-compose up -d redis
Start-Sleep -Seconds 2

docker-compose up -d api-php
Start-Sleep -Seconds 5

docker-compose up -d api-nginx
Start-Sleep -Seconds 2

docker-compose up -d queue-worker
Start-Sleep -Seconds 2

docker-compose up -d web

# Wait for all services to be ready
Write-Host "Waiting for all services to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Fix SQLite database permissions
Write-Host "Fixing database permissions..." -ForegroundColor Yellow
docker-compose exec api-php chmod 666 database/database.sqlite

# Run database migrations
Write-Host "Running database migrations..." -ForegroundColor Yellow
docker-compose exec api-php php artisan migrate --force

# Seed the database with demo data
Write-Host "Seeding database with demo data..." -ForegroundColor Yellow
docker-compose exec api-php php artisan db:seed --force

# Create demo users
Write-Host "Creating demo users..." -ForegroundColor Yellow
docker-compose exec api-php php artisan db:seed --class=UserSeeder

# Check services status
Write-Host "Checking services status..." -ForegroundColor Yellow
docker-compose ps

# Test API
Write-Host "Testing API..." -ForegroundColor Yellow
try {
    $response = Invoke-WebRequest -Uri "http://localhost:8080/health" -UseBasicParsing -TimeoutSec 10
    if ($response.StatusCode -eq 200) {
        Write-Host "API is working correctly!" -ForegroundColor Green
    } else {
        Write-Host "API returned error code: $($response.StatusCode)" -ForegroundColor Red
    }
} catch {
    Write-Host "Error testing API: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Restarting API services..." -ForegroundColor Yellow
    docker-compose restart api-php api-nginx
    Start-Sleep -Seconds 5
    
    # Retry test
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:8080/health" -UseBasicParsing -TimeoutSec 10
        if ($response.StatusCode -eq 200) {
            Write-Host "API is working after restart!" -ForegroundColor Green
        }
    } catch {
        Write-Host "API is still not working. Check logs with: docker-compose logs api-php" -ForegroundColor Red
    }
}

Write-Host "Development environment ready!" -ForegroundColor Green
Write-Host "Frontend: http://localhost:5173" -ForegroundColor Cyan
Write-Host "API: http://localhost:8080" -ForegroundColor Cyan
Write-Host "Redis: localhost:6379" -ForegroundColor Cyan
Write-Host ""
Write-Host "Demo accounts created:" -ForegroundColor Yellow
Write-Host "Admin: admin@acme.test / password" -ForegroundColor White
Write-Host "User: user@acme.test / password" -ForegroundColor White
Write-Host "Creator: creator@acme.test / password" -ForegroundColor White
