# 🎯 ACME Donations Platform

> **A modern, scalable charitable donation platform built with Laravel 12, Vue 3, and Docker**

[![PHP 8.4](https://img.shields.io/badge/PHP-8.4-777BB4?style=flat-square&logo=php)](https://php.net)
[![Laravel 12](https://img.shields.io/badge/Laravel-12.x-FF2D20?style=flat-square&logo=laravel)](https://laravel.com)
[![Vue 3](https://img.shields.io/badge/Vue-3.5.x-4FC08D?style=flat-square&logo=vue.js)](https://vuejs.org)
[![TypeScript](https://img.shields.io/badge/TypeScript-5.x-3178C6?style=flat-square&logo=typescript)](https://typescriptlang.org)
[![Docker](https://img.shields.io/badge/Docker-Compose-2496ED?style=flat-square&logo=docker)](https://docker.com)

## 🚀 Quick Start (TL;DR)

### Prerequisites
- **Docker & Docker Compose** (latest version)
- **Git** (for cloning the repository)

### One-Command Setup

```bash
# 1. Clone the repository
git clone <repository-url> acme-donations
cd acme-donations

# 2. Start everything with database seeding (choose your platform)

# Windows (PowerShell)
.\scripts\dev.ps1 dev-setup

# Linux/Mac (Make)
make dev-setup

# Or manually with Docker Compose
docker-compose --profile dev up -d --build
docker-compose exec api-php php artisan migrate
docker-compose exec api-php php artisan db:seed
```

### 🎭 Demo Accounts (Auto-created)
- **Admin**: `admin@acme.com` / `password`
- **Creator**: `creator@acme.com` / `password`  
- **User**: `user@acme.com` / `password`

### 🌐 Access URLs
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8080
- **API Documentation**: http://localhost:8080/api/documentation

### 🧪 Quick Testing
```bash
# Run all tests
.\scripts\dev.ps1 test          # Windows
make test                       # Linux/Mac

# Run static analysis
.\scripts\dev.ps1 phpstan       # Windows  
make phpstan                    # Linux/Mac
```

### 🔧 Troubleshooting

**Services not starting?**
```bash
# Check service status
.\scripts\dev.ps1 status        # Windows
make status                     # Linux/Mac

# View logs
.\scripts\dev.ps1 logs          # Windows
make logs                       # Linux/Mac
```

**Database issues?**
```bash
# Reset database completely
.\scripts\dev.ps1 fresh         # Windows
make fresh                      # Linux/Mac
```

**Port conflicts?**
- Frontend (5173): Change in `docker-compose.yml` and `web/package.json`
- Backend (8080): Change in `docker-compose.yml` and `api/.env`

**502 Bad Gateway?**
```bash
# Restart API services
.\scripts\dev.ps1 restart       # Windows
make restart                    # Linux/Mac
```

---

## 📋 Table of Contents

- [🎯 Overview](#-overview)
- [🏗️ Architecture](#️-architecture)
- [📁 Project Structure](#-project-structure)
- [🔧 Technology Stack](#-technology-stack)
- [🧪 Testing](#-testing)
- [📊 Quality Assurance](#-quality-assurance)
- [🔒 Security Features](#-security-features)
- [⚡ Performance & Scalability](#-performance--scalability)
- [🔄 Development Workflow](#-development-workflow)
- [📚 API Documentation](#-api-documentation)
- [🐳 Docker Configuration](#-docker-configuration)
- [🤝 Contributing](#-contributing)

## 🎯 Overview

ACME Donations is a comprehensive charitable donation platform designed for modern organizations. It provides a complete solution for campaign management, donation processing, user notifications, and administrative oversight.

### ✨ Key Features

- **🎨 Modern UI/UX**: Responsive design with Vue 3 + Tailwind CSS
- **🔐 Secure Authentication**: Laravel Sanctum with SPA cookies
- **📱 Real-time Notifications**: User notification system
- **👥 Role-based Access**: Admin, Creator, and User roles
- **💳 Payment Processing**: Mock gateway ready for Stripe integration
- **📊 Analytics Dashboard**: Comprehensive campaign and donation insights
- **🔄 Asynchronous Processing**: Queue-based donation processing
- **🧪 Comprehensive Testing**: Unit, Feature, and E2E tests

## 🏗️ Architecture

### 🏛️ Monorepo Structure

```
acme-donations/
├── api/                    # Laravel 12 Backend
│   ├── app/               # Application logic
│   ├── Modules/           # Modular architecture
│   ├── database/          # Migrations, seeders, factories
│   └── tests/             # Backend tests
├── web/                   # Vue 3 Frontend
│   ├── src/               # Source code
│   ├── tests/             # Frontend tests
│   └── dist/              # Built assets
├── shared/                # Shared resources
│   └── openapi/           # API specifications
└── docker-compose.yml     # Development environment
```

### 🔧 Modular Backend Design

The backend follows a **modular architecture** using `nwidart/laravel-modules` for future microservices migration:

- **Auth Module**: User authentication and authorization
- **Campaign Module**: Campaign management and CRUD operations
- **Donation Module**: Donation processing and tracking
- **Payment Module**: Payment gateway abstraction
- **Admin Module**: Administrative functions and analytics
- **Notification Module**: User notification system

### 🌐 API-First Approach

- **OpenAPI Specification**: Complete API documentation in `shared/openapi/`
- **Type Generation**: Automatic TypeScript types from OpenAPI specs
- **Contract-Driven Development**: Frontend and backend communicate via well-defined contracts


## 📁 Project Structure

### 🔧 Backend (`api/`)

```
api/
├── app/
│   ├── Http/Controllers/     # API Controllers
│   ├── Models/              # Eloquent Models
│   ├── Services/            # Business Logic
│   ├── Jobs/                # Queue Jobs
│   └── Contracts/           # Interfaces
├── Modules/                 # Modular Components
│   ├── Auth/               # Authentication
│   ├── Campaign/           # Campaign Management
│   ├── Donation/           # Donation Processing
│   ├── Payment/            # Payment Gateway
│   ├── Admin/              # Admin Functions
│   └── Notification/       # Notifications
├── database/
│   ├── migrations/         # Database Schema
│   ├── seeders/           # Data Seeders
│   └── factories/         # Test Factories
└── tests/
    ├── Unit/              # Unit Tests
    └── Feature/           # Integration Tests
```

### 🎨 Frontend (`web/`)

```
web/
├── src/
│   ├── components/         # Vue Components
│   ├── views/             # Page Components
│   ├── stores/            # Pinia State Management
│   ├── router/            # Vue Router
│   ├── api/               # API Client
│   └── composables/       # Vue Composables
├── tests/
│   └── e2e/               # End-to-End Tests
└── dist/                  # Built Assets
```

## 🔧 Technology Stack

### 🖥️ Backend

| Technology | Version | Purpose |
|------------|---------|---------|
| **PHP** | 8.4 | Runtime environment |
| **Laravel** | 12.x | Web framework |
| **Laravel Sanctum** | 4.x | SPA authentication |
| **Spatie Permission** | 6.x | Role-based access control |
| **nwidart/laravel-modules** | 12.x | Modular architecture |
| **SQLite** | 3.x | Development database |
| **Redis** | 7.x | Caching and sessions |
| **Pest** | 3.x | Testing framework |
| **PHPStan** | 2.x | Static analysis |

### 🎨 Frontend

| Technology | Version | Purpose |
|------------|---------|---------|
| **Vue.js** | 3.5.x | Frontend framework |
| **TypeScript** | 5.x | Type safety |
| **Vite** | 5.x | Build tool |
| **Pinia** | 2.x | State management |
| **Vue Router** | 4.x | Client-side routing |
| **Tailwind CSS** | 4.1.x | Utility-first CSS |
| **Axios** | 1.x | HTTP client |
| **Vue Toastification** | 2.x | Notifications |
| **Playwright** | 1.x | E2E testing |

### 🐳 Infrastructure

| Technology | Purpose |
|------------|---------|
| **Docker Compose** | Development environment |
| **Nginx** | Web server |
| **PHP-FPM** | PHP process manager |
| **Redis** | Cache and session store |

## 🧪 Testing

### 🔬 Backend Testing

```bash
# Using Docker Compose directly
docker-compose exec api-php ./vendor/bin/pest
docker-compose exec api-php ./vendor/bin/pest tests/Unit/
docker-compose exec api-php ./vendor/bin/pest tests/Feature/

# Using PowerShell script (Windows)
.\scripts\dev.ps1 test
.\scripts\dev.ps1 test-unit
.\scripts\dev.ps1 test-feature

# Using Make (Linux/Mac)
make test
make test-unit
make test-feature
```

### 🎭 Frontend Testing

```bash
# Unit tests
docker-compose exec web npm run test

# E2E tests
docker-compose exec web npm run e2e

# Type checking
docker-compose exec web npm run type-check
```

### 📊 Test Coverage

- **Unit Tests**: 100% model coverage
- **Feature Tests**: 60% controller coverage
- **E2E Tests**: Critical user journeys

## 📊 Quality Assurance

### 🔍 Static Analysis

```bash
# Using Docker Compose directly
docker-compose exec api-php ./vendor/bin/phpstan analyse --level=8
docker-compose exec api-php ./vendor/bin/pint
docker-compose exec web npm run lint

# Using PowerShell script (Windows)
.\scripts\dev.ps1 phpstan
.\scripts\dev.ps1 quality-check

# Using Make (Linux/Mac)
make phpstan
make quality-check
```

### 🎯 Quality Metrics

- **PHPStan Level 8**: Maximum static analysis strictness
- **TypeScript Strict Mode**: Full type safety
- **PSR-12 Compliance**: PHP coding standards
- **ESLint Configuration**: Frontend code quality

## 🔒 Security Features

### 🛡️ Authentication & Authorization

- **Laravel Sanctum**: SPA authentication with secure cookies
- **CSRF Protection**: Cross-site request forgery prevention
- **Role-based Access Control**: Admin, Creator, User roles
- **Secure Headers**: CORS, CSP, and security headers

### 🔐 Data Protection

- **Password Hashing**: Bcrypt with configurable rounds
- **SQL Injection Prevention**: Eloquent ORM with parameterized queries
- **XSS Protection**: Input sanitization and output escaping
- **Session Security**: HttpOnly, Secure, SameSite cookies

## ⚡ Performance & Scalability

### 🚀 Optimization Strategies

- **Redis Caching**: Campaign listings and statistics
- **Queue Processing**: Asynchronous donation processing
- **Database Indexing**: Optimized queries with proper indexes
- **Frontend Optimization**: Code splitting and lazy loading

### 📈 Scalability Considerations

- **Modular Architecture**: Easy extraction to microservices
- **Database Agnostic**: SQLite for development, PostgreSQL for production
- **Horizontal Scaling**: Stateless application design
- **CDN Ready**: Static asset optimization

## 🔄 Development Workflow

### 🛠️ Development Commands

```bash
# Start development environment
docker-compose --profile dev up -d

# View logs
docker-compose logs -f api-php
docker-compose logs -f web

# Run migrations
docker-compose exec api-php php artisan migrate

# Seed database
docker-compose exec api-php php artisan db:seed

# Clear caches
docker-compose exec api-php php artisan cache:clear
docker-compose exec api-php php artisan config:clear
```

### 🔧 Build Commands

```bash
# Build frontend for production
docker-compose exec web npm run build

# Generate API types
docker-compose exec web npm run generate:types

# Run all quality checks
make quality-check
```

## 📚 API Documentation

### 📖 OpenAPI Specification

The complete API documentation is available in `shared/openapi/acme.yaml`:

- **Campaign Management**: CRUD operations for campaigns
- **Donation Processing**: Payment and donation tracking
- **User Management**: Authentication and profile management
- **Admin Functions**: Analytics and campaign moderation
- **Notifications**: User notification system

### 🔗 API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/campaigns` | GET | List campaigns |
| `/api/campaigns` | POST | Create campaign |
| `/api/campaigns/{id}` | GET | Get campaign details |
| `/api/donations` | POST | Process donation |
| `/api/admin/dashboard` | GET | Admin dashboard data |
| `/api/me/notifications` | GET | User notifications |

## 🐳 Docker Configuration

### 🏗️ Service Architecture

```yaml
services:
  api-php:      # Laravel application
  api-nginx:    # Web server
  queue-worker: # Background job processing
  web:          # Vue.js development server
  redis:        # Cache and session store
```

### 🔧 Environment Profiles

- **Development** (`--profile dev`): Full development environment
- **Production** (`--profile prod`): Optimized production build

### 📊 Resource Requirements

- **Minimum**: 2GB RAM, 2 CPU cores
- **Recommended**: 4GB RAM, 4 CPU cores
- **Storage**: 2GB for dependencies and data

## 🤝 Contributing

### 📋 Development Guidelines

1. **Code Style**: Follow PSR-12 (PHP) and ESLint (TypeScript)
2. **Testing**: Write tests for new features
3. **Documentation**: Update API docs for new endpoints
4. **Commits**: Use conventional commit messages

### 🔄 Git Workflow

```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes and test
docker-compose exec api-php ./vendor/bin/pest
docker-compose exec api-php ./vendor/bin/phpstan analyse --level=8

# Commit changes
git commit -m "feat: add new feature"

# Push and create PR
git push origin feature/new-feature
```

## 📞 Support

For questions, issues, or contributions:

- **Issues**: Create a GitHub issue
- **Documentation**: Check the API docs in `shared/openapi/`
- **Development**: Follow the development workflow above

---

**Built with ❤️ using Laravel, Vue.js, and modern web technologies**
