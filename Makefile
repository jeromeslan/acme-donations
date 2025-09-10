SHELL := /usr/bin/env bash

.PHONY: up down logs api.seed web.dev qa e2e build-prod up-prod seed-prod stop clean ps
 .PHONY: api.build web.build build web.types api.optimize api.migrate

ps:
	docker compose ps

up:
	docker compose --profile dev up -d --build

down:
	docker compose down -v

logs:
	docker compose logs -f --tail=200

api.seed:
	docker compose exec -T api-php sh -lc "php artisan migrate --force && php artisan db:seed --class=DemoSeeder --force"

web.dev:
	docker compose --profile dev up -d web

qa:
	# Backend QA
	docker compose exec -T api-php sh -lc "php -v && php artisan --version || true && vendor/bin/phpstan analyse --no-progress --level=8 || true && vendor/bin/pest --colors=always || true"
	# Frontend QA
	docker compose run --rm web npm run lint -- --max-warnings=0 || true
	docker compose run --rm web npm run type-check || true
	docker compose run --rm web npm run test -- --run || true
	# Generate API types
	docker compose run --rm web npm run generate:types || true

e2e:
	# Run Playwright e2e in web container
	docker compose run --rm web npm run e2e

build-prod:
	# Build web assets
	docker compose run --rm web npm ci && docker compose run --rm web npm run build

up-prod:
	docker compose --profile prod up -d --build

seed-prod:
	docker compose exec -T api-php sh -lc "php artisan migrate --force && php artisan db:seed --class=DemoSeeder --force"

stop:
	docker compose stop

clean:
	docker compose down -v --remove-orphans

# Build without rebuilding Docker images
api.build:
	docker compose exec -T api-php sh -lc "export COMPOSER_MEMORY_LIMIT=-1 COMPOSER_PROCESS_TIMEOUT=1800; composer install --no-interaction --prefer-dist || composer update -W --no-interaction --prefer-dist; composer dump-autoload -o; php artisan optimize:clear || true; php artisan optimize || true"

web.build:
	docker compose run --rm web npm ci
	docker compose run --rm web npm run build

build: api.build web.build

# Optional helpers
web.types:
	docker compose run --rm web npx openapi-typescript /app/shared/openapi/acme.yaml -o src/api/types.ts

api.optimize:
	docker compose exec -T api-php sh -lc "php artisan optimize:clear && php artisan optimize || true"

api.migrate:
	docker compose exec -T api-php sh -lc "php artisan migrate --force"


