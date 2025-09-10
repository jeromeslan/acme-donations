acme-donations monorepo

Monorepo API-first et modulaire pour ACME CSR/Donations.

- api/: Laravel 12.x (PHP 8.4), Sanctum (cookies), RBAC (spatie), Redis, queues, modules (nwidart)
- web/: Vue 3.5.21 + Vite + TypeScript + Pinia + Vue Router + Tailwind 4.1.13
- shared/: OpenAPI, types/DTO, conventions

Prerequis
- Docker Desktop + Docker Compose v2
- Node 20 LTS (optionnel local)

One-liner démo (build + migrations/seed + démarrage)

PowerShell (Windows):
```
./scripts/dev.ps1
```

Bash (macOS/Linux/WSL):
```
bash ./scripts/dev.sh
```

Services
- API: http://localhost:8080
- Web (Vite dev): http://localhost:5173
- Redis: localhost:6379

Docs complémentaires: voir `api/README.md`, `web/README.md`, `shared/README.md`.


