# Web (Vue 3.5.21 + Vite + TS + Pinia + Router + Tailwind 4.1.13)

- Client HTTP typé depuis `shared/openapi/acme.yaml` via `openapi-typescript`.
- Auth Sanctum en cookies (credentials inclus), flux CSRF, 401/419 handling.

## Dev
- `npm ci`
- `npm run dev`

## Build
- `npm run build`


I'll fix the Laravel bootstrap by replacing the copy approach with a clean create-project in-place (preserving Docker files), then restart the API containers and verify /health and web logs.

[4 tools called]

I’m going to fix the missing vendor dependencies inside the API container, run migrations, then verify both API /health and the web dev server are reachable. Then I’ll show you how to view logs correctly in PowerShell without piping to cat.

[1 tool called]

I’m going to finish the API bootstrap by allowing the Composer pest plugin, installing dependencies, running migrations/seed, then verify the API health and inspect web logs using PowerShell-friendly commands.

[2 tools called]

I'll install the missing Laravel Pail package, rerun migrations, and then verify the API health and web service status.

[2 tools called]

I'll inspect `api/composer.json` to see why Laravel downgraded and fix dependencies. Then I'll install missing packages or re-upgrade to Laravel 12, run migrations and verify health.

[1 tool called]

I regenerated a clean Laravel 12 composer.json in the API container and will install dependencies and run migrations. Then I’ll verify health and guide you on viewing Docker logs in PowerShell without piping to cat.

[1 tool called]

I'll quickly verify containers are running, probe the API /health endpoint using PowerShell-safe syntax, and show recent web logs without piping to cat.

[3 tools called]

Oui. Le stack Docker est up et testable.

- API: `http://localhost:8080/health` renvoie 200
- Web (Vite dev): `http://localhost:5173/` est prêt

Commandes utiles (PowerShell):
- Voir l’état: `docker compose ps`
- Logs web: `docker compose logs --tail 80 web`
- Logs API (PHP-FPM): `docker compose logs --tail 80 api-php`
- Logs Nginx: `docker compose logs --tail 80 api-nginx`
- (optionnel) Forcer migrations/seed: `docker compose exec -T api-php sh -lc "php artisan migrate --force && php artisan db:seed --force"`

Note: Inutile de piper vers `cat` sous PowerShell; utilisez directement `docker compose logs --tail …`.

- I verified containers are running, API /health returns 200, and the Vite dev server is up at 5173.



### Quick dev run
- Start services:
```bash
make up
```
- Seed demo data (roles/admin/campaigns/donations/receipts):
```bash
make api.seed
```
- Ensure web dev server is running:
```bash
make web.dev
```

### Open the app
- Web: `http://localhost:5173`
- API health: `http://localhost:8080/health` (should be 200)

### What to click/test in the UI
- In the top bar, click “Login demo” to sign in as admin (`admin@acme.test`).
- Home shows “Featured campaigns”.
- Visit:
  - Admin: `http://localhost:5173/admin` (placeholder dashboard)
  - Creator: `http://localhost:5173/creator` (placeholder workspace)

### API endpoints you can hit now
- List campaigns: `GET http://localhost:8080/api/campaigns`
- Featured campaigns: `GET http://localhost:8080/api/campaigns/featured`
- Campaign details: `GET http://localhost:8080/api/campaigns/{id}`
- Donate (mock): `POST http://localhost:8080/api/campaigns/{id}/donations` body: `{ "amount": 25 }`
- My donations: `GET http://localhost:8080/api/me/donations` (after logging in via web)
- Receipt: `GET http://localhost:8080/api/donations/{id}/receipt`

Notes:
- SPA auth is cookie-based Sanctum; the web app handles CSRF and `withCredentials` automatically.
- If you donate via an API client, fetch `GET /sanctum/csrf-cookie` first, then `POST /api/login`, then the donation.

### QA and types
```bash
make qa
```
- Runs PHPStan, Pest (backend), ESLint, TS type-check, Vitest, and generates OpenAPI types for web.

### Prod build (optional)
```bash
make build-prod && make up-prod && make seed-prod
```
- Web (static): `http://localhost:5173`
- API: `http://localhost:8080`

### Troubleshooting
- Status:
```bash
make ps
```
- Logs (follow):
```bash
make logs
```
- Reset stack:
```bash
make down
make clean
```


owerShell (une seule commande):
.\scripts\build.ps1
Avec génération des types OpenAPI: .\scripts\build.ps1 -Types
Make (si vous avez make):
make build
Ou séparément: make api.build et make web.build
Ces commandes:
Backend: installe/upgrade deps Composer, dump-autoload, optimize (sans rebuild Docker)
Frontend: npm ci + vite build
Optionnel: génère les types OpenAPI pour le front
Les services doivent déjà être up (via .\scripts\dev.ps1 ou docker compose up).


docker compose up -d --build
.\scripts\build.ps1