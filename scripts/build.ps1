Param(
  [switch]$Types
)

# Backend build (deps + autoload + optimize) without rebuilding images
docker compose exec -T api-php sh -lc "export COMPOSER_MEMORY_LIMIT=-1 COMPOSER_PROCESS_TIMEOUT=1800; composer install -o --no-interaction --prefer-dist || composer update -W -o --no-interaction --prefer-dist; composer dump-autoload -o; php artisan optimize:clear || true; php artisan optimize || true"

# Frontend build
docker compose run --rm web npm ci
docker compose run --rm web npm run build

if ($Types) {
  docker compose run --rm web npx openapi-typescript /app/shared/openapi/acme.yaml -o src/api/types.ts | Out-Null
}

Write-Host "Build complete. Web dist in web/dist. API optimized."


