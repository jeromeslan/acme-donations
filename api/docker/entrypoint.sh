#!/usr/bin/env sh
set -e

# Ensure storage and cache perms
mkdir -p storage/framework/{cache,sessions,views} storage/logs database
chmod -R 777 storage bootstrap/cache || true

# Bootstrap Laravel app if missing (bind-mount dev)
if [ ! -f artisan ]; then
  echo "[entrypoint] Bootstrapping fresh Laravel app in-place..."
  mkdir -p /tmp/keep
  mv -f Dockerfile /tmp/keep/ 2>/dev/null || true
  mv -f README.md /tmp/keep/ 2>/dev/null || true
  mv -f docker /tmp/keep/ 2>/dev/null || true
  # Remove everything else in project root
  find . -mindepth 1 -maxdepth 1 \
    ! -name '.' \
    ! -name '..' \
    ! -name 'tmp' \
    ! -name 'keep' \
    ! -name '.git' \
    -exec rm -rf {} +
  composer create-project laravel/laravel:^12.0 . --no-interaction --prefer-dist || true
  mv -f /tmp/keep/* . 2>/dev/null || true
  rmdir /tmp/keep 2>/dev/null || true
  composer require --no-interaction \
    laravel/sanctum:^4.0 \
    spatie/laravel-permission:^6.10 \
    nwidart/laravel-modules:^11.0 \
    darkaonline/l5-swagger:^9.0 \
    laravel/horizon:^6.0 || true
  composer require --dev --no-interaction \
    pestphp/pest:^3.0 pestphp/pest-plugin-laravel:^3.0 \
    phpstan/phpstan:^1.11 nunomaduro/larastan:^2.9 || true
fi

# Generate app key if missing
if ! grep -q "^APP_KEY=" .env 2>/dev/null; then
  cp -n .env.example .env || true
  php artisan key:generate || true
fi

# SQLite database for dev if configured
if [ "${DB_CONNECTION:-sqlite}" = "sqlite" ]; then
  touch database/database.sqlite || true
fi

# Publish vendor assets/configs if not present
[ -f config/sanctum.php ] || php artisan vendor:publish --provider="Laravel\\Sanctum\\SanctumServiceProvider" --force || true
[ -d config/l5-swagger.php ] || php artisan vendor:publish --provider="DarkaOnLine\\L5Swagger\\L5SwaggerServiceProvider" --force || true
[ -f config/modules.php ] || php artisan vendor:publish --provider="Nwidart\\Modules\\LaravelModulesServiceProvider" --force || true
[ -f config/horizon.php ] || php artisan vendor:publish --provider="Laravel\\Horizon\\HorizonServiceProvider" --force || true

# Ensure base tables exist
php artisan migrate --force || true

# Skip module creation - using main app only for now
# for module in Campaign Donation; do
#   if ! php artisan module:list | grep -q "^$module" 2>/dev/null; then
#     php artisan module:make "$module" || true
#   fi
# done

exec "$@"


