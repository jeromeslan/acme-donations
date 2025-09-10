#!/usr/bin/env bash
set -euo pipefail

if [[ "${1:-}" == "--rebuild" ]]; then
  docker compose build --no-cache
fi

docker compose up -d --build

echo "Waiting for API to be ready..."
for i in {1..60}; do
  if curl -sf http://localhost:8080/health >/dev/null; then
    break
  fi
  sleep 2
done

echo "Running migrations and seeders..."
docker compose exec -T api-php sh -lc "php artisan migrate --force && php artisan db:seed --force || true"

echo "Web: http://localhost:5173  |  API: http://localhost:8080"


