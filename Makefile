# ACME Donations Platform - Development Commands
# ================================================

.PHONY: help build start stop restart logs test quality-check clean

# Default target
help: ## Show this help message
	@echo "ACME Donations Platform - Development Commands"
	@echo "=============================================="
	@echo ""
	@echo "Available commands:"
	@awk 'BEGIN {FS = ":.*?## "} /^[a-zA-Z_-]+:.*?## / {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}' $(MAKEFILE_LIST)

# Build and start services
build: ## Build and start all services
	docker-compose --profile dev up -d --build

start: ## Start all services
	docker-compose --profile dev up -d

stop: ## Stop all services
	docker-compose --profile dev down

restart: ## Restart all services
	docker-compose --profile dev restart

# Development commands
logs: ## Show logs for all services
	docker-compose logs -f

logs-api: ## Show API logs
	docker-compose logs -f api-php

logs-web: ## Show web logs
	docker-compose logs -f web

# Database commands
migrate: ## Run database migrations
	docker-compose exec api-php php artisan migrate

seed: ## Seed the database
	docker-compose exec api-php php artisan db:seed

fresh: ## Fresh migration and seed
	docker-compose exec api-php php artisan migrate:fresh --seed

# Testing commands
test: ## Run all tests
	docker-compose exec api-php ./vendor/bin/pest

test-unit: ## Run unit tests
	docker-compose exec api-php ./vendor/bin/pest tests/Unit/

test-feature: ## Run feature tests
	docker-compose exec api-php ./vendor/bin/pest tests/Feature/

test-coverage: ## Run tests with coverage
	docker-compose exec api-php ./vendor/bin/pest --coverage

# Quality assurance
phpstan: ## Run PHPStan static analysis
	docker-compose exec api-php ./vendor/bin/phpstan analyse --level=8

pint: ## Run Laravel Pint code formatting
	docker-compose exec api-php ./vendor/bin/pint

quality-check: phpstan test ## Run all quality checks

# Frontend commands
web-test: ## Run frontend tests
	docker-compose exec web npm run test

web-e2e: ## Run E2E tests
	docker-compose exec web npm run e2e

web-build: ## Build frontend for production
	docker-compose exec web npm run build

web-lint: ## Run frontend linting
	docker-compose exec web npm run lint

# Cache and optimization
clear-cache: ## Clear all caches
	docker-compose exec api-php php artisan cache:clear
	docker-compose exec api-php php artisan config:clear
	docker-compose exec api-php php artisan route:clear
	docker-compose exec api-php php artisan view:clear

optimize: ## Optimize for production
	docker-compose exec api-php php artisan config:cache
	docker-compose exec api-php php artisan route:cache
	docker-compose exec api-php php artisan view:cache

# Cleanup commands
clean: ## Clean up containers and volumes
	docker-compose --profile dev down -v
	docker system prune -f

clean-all: ## Clean up everything including images
	docker-compose --profile dev down -v --rmi all
	docker system prune -af

# Production commands
prod-build: ## Build for production
	docker-compose --profile prod up -d --build

prod-start: ## Start production services
	docker-compose --profile prod up -d

# Development utilities
shell-api: ## Open shell in API container
	docker-compose exec api-php bash

shell-web: ## Open shell in web container
	docker-compose exec web sh

install: ## Install dependencies
	docker-compose exec api-php composer install
	docker-compose exec web npm install

# Quick development setup
dev-setup: build migrate seed ## Complete development setup
	@echo "✅ Development environment ready!"
	@echo "🌐 Frontend: http://localhost:5173"
	@echo "🔧 Backend: http://localhost:8080"
	@echo "📚 API Docs: http://localhost:8080/api/documentation"

# Status check
status: ## Check service status
	docker-compose ps