# API (Laravel 12.x, PHP 8.4)

- Auth: Sanctum (SPA cookies stateful)
- RBAC: spatie/laravel-permission
- Modules: nwidart/laravel-modules (Auth, Campaign, Donation, Payment, Admin, Notification)
- Cache/Sessions/Queues: Redis (queues: database in dev, Redis-ready)
- Tests: Pest; Static analysis: PHPStan level 8 (Larastan)
- OpenAPI: l5-swagger, spec source in `shared/openapi/acme.yaml`

## Local
- `.env` based on `.env.example`
- `php artisan migrate --force && php artisan db:seed --force`

## Docker
- Served by `api-php` + `api-nginx` in docker-compose
- Health: `/health`
