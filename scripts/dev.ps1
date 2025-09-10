Param(
  [switch]$Rebuild,
  [switch]$Prod,
  [switch]$Repair
)

$profile = if ($Prod) { 'prod' } else { 'dev' }

if ($Rebuild) { docker compose --profile $profile build --no-cache }
docker compose --profile $profile up -d --build

# Backend: ensure dependencies installed and app ready
Write-Host "Ensuring backend dependencies and app bootstrap..."
$composerCmd = "export COMPOSER_MEMORY_LIMIT=-1 COMPOSER_PROCESS_TIMEOUT=1800; composer config --no-interaction allow-plugins.pestphp/pest-plugin true; composer install --no-interaction --prefer-dist || composer update -W --no-interaction --prefer-dist"
if ($Repair) {
  $composerCmd = "export COMPOSER_MEMORY_LIMIT=-1 COMPOSER_PROCESS_TIMEOUT=1800; composer remove --dev laravel/sail -W || true; composer update -W --no-interaction --prefer-dist"
}
docker compose exec -T api-php sh -lc "$composerCmd && composer dump-autoload -o"
# Ensure required Laravel packages are installed (idempotent)
docker compose exec -T api-php sh -lc "composer require --no-interaction -W laravel/sanctum:^4.0 spatie/laravel-permission:^6.10 nwidart/laravel-modules:^11.0 darkaonline/l5-swagger:^9.0 laravel/horizon:^6.0 || true && composer dump-autoload -o"
docker compose exec -T api-php sh -lc "rm -f bootstrap/cache/*.php; php artisan optimize:clear || true; touch database/database.sqlite || true"

# Migrate/seed demo
Write-Host "Running migrations and seeders..."
docker compose exec -T api-php sh -lc "php artisan migrate --force && (php artisan db:seed --class=DemoSeeder --force || php artisan db:seed --force) || true"

# Frontend: generate OpenAPI types (dev only)
if (-not $Prod) {
  Write-Host "Generating frontend API types from OpenAPI..."
  docker compose run --rm web npx openapi-typescript /app/shared/openapi/acme.yaml -o src/api/types.ts | Out-Null
}

# Wait for API health
Write-Host "Waiting for API to be ready..."
$tries = 0
while ($tries -lt 90) {
  try {
    $res = Invoke-WebRequest -UseBasicParsing http://localhost:8080/health
    if ($res.StatusCode -eq 200) { break }
  } catch {}
  Start-Sleep -Seconds 2
  $tries++
}

Write-Host "Open Web: http://localhost:5173  |  API: http://localhost:8080"


