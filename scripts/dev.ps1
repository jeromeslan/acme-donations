# ACME Donations Platform - Development Scripts
# ==============================================

param(
    [Parameter(Position=0)]
    [string]$Command = "help"
)

function Show-Help {
    Write-Host "ACME Donations Platform - Development Commands" -ForegroundColor Cyan
    Write-Host "==============================================" -ForegroundColor Cyan
    Write-Host ""
    Write-Host "Available commands:" -ForegroundColor Yellow
    Write-Host "  build          Build and start all services" -ForegroundColor Green
    Write-Host "  start          Start all services" -ForegroundColor Green
    Write-Host "  stop           Stop all services" -ForegroundColor Green
    Write-Host "  restart        Restart all services" -ForegroundColor Green
    Write-Host "  logs           Show logs for all services" -ForegroundColor Green
    Write-Host "  logs-api       Show API logs" -ForegroundColor Green
    Write-Host "  logs-web       Show web logs" -ForegroundColor Green
    Write-Host "  migrate        Run database migrations" -ForegroundColor Green
    Write-Host "  seed           Seed the database" -ForegroundColor Green
    Write-Host "  fresh          Fresh migration and seed" -ForegroundColor Green
    Write-Host "  test           Run all tests" -ForegroundColor Green
    Write-Host "  test-unit      Run unit tests" -ForegroundColor Green
    Write-Host "  test-feature   Run feature tests" -ForegroundColor Green
    Write-Host "  phpstan        Run PHPStan static analysis" -ForegroundColor Green
    Write-Host "  quality-check  Run all quality checks" -ForegroundColor Green
    Write-Host "  web-test       Run frontend tests" -ForegroundColor Green
    Write-Host "  web-e2e        Run E2E tests" -ForegroundColor Green
    Write-Host "  web-build      Build frontend for production" -ForegroundColor Green
    Write-Host "  clear-cache    Clear all caches" -ForegroundColor Green
    Write-Host "  clean          Clean up containers and volumes" -ForegroundColor Green
    Write-Host "  dev-setup      Complete development setup" -ForegroundColor Green
    Write-Host "  status         Check service status" -ForegroundColor Green
    Write-Host ""
    Write-Host "Usage: .\scripts\dev.ps1 <command>" -ForegroundColor Yellow
}

function Invoke-DockerCompose {
    param([string]$Args)
    docker-compose --profile dev $Args
}

function Invoke-DockerExec {
    param([string]$Service, [string]$Command)
    docker-compose exec $Service $Command
}

switch ($Command.ToLower()) {
    "help" { Show-Help }
    
    "build" { 
        Write-Host "Building and starting all services..." -ForegroundColor Yellow
        Invoke-DockerCompose "up -d --build"
    }
    
    "start" { 
        Write-Host "Starting all services..." -ForegroundColor Yellow
        Invoke-DockerCompose "up -d"
    }
    
    "stop" { 
        Write-Host "Stopping all services..." -ForegroundColor Yellow
        Invoke-DockerCompose "down"
    }
    
    "restart" { 
        Write-Host "Restarting all services..." -ForegroundColor Yellow
        Invoke-DockerCompose "restart"
    }
    
    "logs" { 
        Write-Host "Showing logs for all services..." -ForegroundColor Yellow
        Invoke-DockerCompose "logs -f"
    }
    
    "logs-api" { 
        Write-Host "Showing API logs..." -ForegroundColor Yellow
        Invoke-DockerCompose "logs -f api-php"
    }
    
    "logs-web" { 
        Write-Host "Showing web logs..." -ForegroundColor Yellow
        Invoke-DockerCompose "logs -f web"
    }
    
    "migrate" { 
        Write-Host "Running database migrations..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "php artisan migrate"
    }
    
    "seed" { 
        Write-Host "Seeding the database..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "php artisan db:seed"
    }
    
    "fresh" { 
        Write-Host "Running fresh migration and seed..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "php artisan migrate:fresh --seed"
    }
    
    "test" { 
        Write-Host "Running all tests..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "./vendor/bin/pest"
    }
    
    "test-unit" { 
        Write-Host "Running unit tests..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "./vendor/bin/pest tests/Unit/"
    }
    
    "test-feature" { 
        Write-Host "Running feature tests..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "./vendor/bin/pest tests/Feature/"
    }
    
    "phpstan" { 
        Write-Host "Running PHPStan static analysis..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "./vendor/bin/phpstan analyse --level=8"
    }
    
    "quality-check" { 
        Write-Host "Running all quality checks..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "./vendor/bin/phpstan analyse --level=8"
        Invoke-DockerExec "api-php" "./vendor/bin/pest"
    }
    
    "web-test" { 
        Write-Host "Running frontend tests..." -ForegroundColor Yellow
        Invoke-DockerExec "web" "npm run test"
    }
    
    "web-e2e" { 
        Write-Host "Running E2E tests..." -ForegroundColor Yellow
        Invoke-DockerExec "web" "npm run e2e"
    }
    
    "web-build" { 
        Write-Host "Building frontend for production..." -ForegroundColor Yellow
        Invoke-DockerExec "web" "npm run build"
    }
    
    "clear-cache" { 
        Write-Host "Clearing all caches..." -ForegroundColor Yellow
        Invoke-DockerExec "api-php" "php artisan cache:clear"
        Invoke-DockerExec "api-php" "php artisan config:clear"
        Invoke-DockerExec "api-php" "php artisan route:clear"
        Invoke-DockerExec "api-php" "php artisan view:clear"
    }
    
    "clean" { 
        Write-Host "Cleaning up containers and volumes..." -ForegroundColor Yellow
        Invoke-DockerCompose "down -v"
        docker system prune -f
    }
    
    "dev-setup" { 
        Write-Host "Setting up complete development environment..." -ForegroundColor Yellow
        Invoke-DockerCompose "up -d --build"
        Start-Sleep -Seconds 30
        Invoke-DockerExec "api-php" "php artisan migrate"
        Invoke-DockerExec "api-php" "php artisan db:seed"
        Write-Host "✅ Development environment ready!" -ForegroundColor Green
        Write-Host "🌐 Frontend: http://localhost:5173" -ForegroundColor Cyan
        Write-Host "🔧 Backend: http://localhost:8080" -ForegroundColor Cyan
        Write-Host "📚 API Docs: http://localhost:8080/api/documentation" -ForegroundColor Cyan
    }
    
    "status" { 
        Write-Host "Checking service status..." -ForegroundColor Yellow
        Invoke-DockerCompose "ps"
    }
    
    default { 
        Write-Host "Unknown command: $Command" -ForegroundColor Red
        Show-Help
    }
}