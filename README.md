# ACME Donations Platform

A modern, full-stack charitable donation platform built with Laravel 12.x and Vue.js 3.5.x, designed for scalability and maintainability.

## 🚀 Quick Start

### Prerequisites
- Docker & Docker Compose
- Git

### One-Command Setup

**Windows (PowerShell):**
```powershell
.\scripts\start-dev.ps1
```

**Linux/Mac:**
```bash
./scripts/start-dev.sh
```

**Alternative (Make):**
```bash
make dev-setup
```

### Access URLs
- **Frontend**: http://localhost:5173
- **Backend API**: http://localhost:8080
- **API Documentation**: http://localhost:8080/api/documentation

### 🎭 Demo Accounts (Auto-created)
- **Admin**: `admin@acme.test` / `password`
- **Creator**: `creator@acme.test` / `password`  
- **User**: `user@acme.test` / `password`

## 🏗️ Project Structure

```
acme-donations/
├── api/                    # Laravel 12.x Backend
│   ├── app/
│   │   ├── Http/Controllers/    # API Controllers
│   │   ├── Models/             # Eloquent Models
│   │   ├── Jobs/               # Background Jobs
│   │   ├── Services/           # Business Logic
│   │   └── Contracts/          # Interfaces
│   ├── database/
│   │   ├── migrations/         # Database Schema
│   │   ├── seeders/           # Demo Data
│   │   └── factories/         # Test Data
│   └── tests/                 # Pest Tests
├── web/                     # Vue.js 3.5.x Frontend
│   ├── src/
│   │   ├── components/        # Vue Components
│   │   ├── views/            # Page Components
│   │   ├── stores/           # Pinia State Management
│   │   └── api/              # API Client
│   └── public/
└── shared/                  # Shared Resources
    └── openapi/             # API Specifications
```

## 🛠️ Technology Stack

### Backend (Laravel 12.x)
- **PHP 8.4** - Latest PHP version
- **Laravel 12.x** - Modern web framework
- **Laravel Sanctum** - SPA authentication with secure cookies
- **Spatie Laravel Permission** - Role-based access control
- **Redis 7.x** - Caching, sessions, and queue driver
- **SQLite** - Development database (PostgreSQL ready)
- **Laravel Horizon** - Queue monitoring (optional)

### Frontend (Vue.js 3.5.x)
- **Vue 3.5.x** - Progressive JavaScript framework
- **TypeScript** - Type-safe development
- **Vite** - Fast build tool
- **Pinia** - State management
- **Vue Router** - Client-side routing
- **Tailwind CSS 4.1.x** - Utility-first CSS
- **Vue Toastification** - Modern notifications
- **Axios** - HTTP client

### Development & Quality
- **Docker & Docker Compose** - Containerized development
- **PHPStan Level 8** - Static analysis
- **Pest 3.x** - PHP testing framework
- **Vitest** - Frontend testing
- **Playwright** - E2E testing
- **ESLint & Prettier** - Code formatting

## 🔧 Development Commands

### Backend (Laravel)
```bash
# Run PHPStan static analysis
docker-compose exec api-php ./vendor/bin/phpstan analyse --level=8

# Run Pest tests
docker-compose exec api-php ./vendor/bin/pest

# Run migrations
docker-compose exec api-php php artisan migrate

# Seed demo data
docker-compose exec api-php php artisan db:seed

# Create demo users
docker-compose exec api-php php artisan db:seed --class=UserSeeder

# Clear cache
docker-compose exec api-php php artisan cache:clear
```

### Frontend (Vue.js)
```bash
# Install dependencies
docker-compose exec web npm install

# Run development server
docker-compose exec web npm run dev

# Build for production
docker-compose exec web npm run build

# Run tests
docker-compose exec web npm run test

# Run linting
docker-compose exec web npm run lint
```

### Docker Commands
```bash
# Start all services
docker-compose up -d

# Stop all services
docker-compose down

# Rebuild containers
docker-compose build --no-cache

# View logs
docker-compose logs -f [service-name]

# Execute commands in containers
docker-compose exec [service-name] [command]
```

## 🧪 Testing

### Backend Testing (Pest)
```bash
# Run all tests
docker-compose exec api-php ./vendor/bin/pest

# Run specific test file
docker-compose exec api-php ./vendor/bin/pest tests/Feature/CampaignControllerTest.php

# Run with coverage
docker-compose exec api-php ./vendor/bin/pest --coverage
```

### Frontend Testing (Vitest)
```bash
# Run unit tests
docker-compose exec web npm run test

# Run tests with coverage
docker-compose exec web npm run test:coverage
```

### E2E Testing (Playwright)
```bash
# Run E2E tests
docker-compose exec web npm run test:e2e

# Run E2E tests in headed mode
docker-compose exec web npm run test:e2e:headed
```

## 🔍 Quality Assurance

### Static Analysis
```bash
# PHPStan Level 8
docker-compose exec api-php ./vendor/bin/phpstan analyse --level=8

# ESLint (Frontend)
docker-compose exec web npm run lint

# TypeScript check
docker-compose exec web npm run type-check
```

### Code Formatting
```bash
# PHP (Laravel Pint)
docker-compose exec api-php ./vendor/bin/pint

# JavaScript/TypeScript (Prettier)
docker-compose exec web npm run format
```

## 🔐 Security Features

### Authentication & Authorization
- **Laravel Sanctum** - SPA authentication with secure cookies
- **CSRF Protection** - Cross-site request forgery prevention
- **Role-based Access Control** - Admin, Creator, User roles
- **Secure Cookies** - HttpOnly, Secure, SameSite attributes

### Data Protection
- **Input Validation** - Server-side validation for all inputs
- **SQL Injection Prevention** - Eloquent ORM with parameterized queries
- **XSS Protection** - Output escaping and sanitization
- **Rate Limiting** - API rate limiting middleware

### Environment Security
- **Environment Variables** - No secrets in code
- **Database Security** - Prepared statements and migrations
- **File Permissions** - Proper file system permissions
- **Docker Security** - Container isolation

## ⚡ Performance & Scalability

### Caching Strategy
- **Redis Caching** - Campaign listings and statistics
- **Cache Invalidation** - Event-driven cache clearing
- **TTL Management** - Explicit cache expiration
- **Cache Warming** - Background cache preheating

### Database Optimization
- **Eloquent Relationships** - Optimized queries with eager loading
- **Database Indexing** - Strategic indexes for performance
- **Query Optimization** - Efficient database queries
- **Connection Pooling** - Database connection management

### Frontend Performance
- **Code Splitting** - Route-based code splitting
- **Lazy Loading** - Component lazy loading
- **Image Optimization** - Responsive images with modern formats
- **Bundle Optimization** - Tree shaking and minification

### Queue Processing
- **Asynchronous Jobs** - Background donation processing
- **Queue Monitoring** - Laravel Horizon integration
- **Job Retry Logic** - Exponential backoff for failed jobs
- **Queue Scaling** - Multiple worker processes

## 🔄 Development Workflow

### Git Workflow
```bash
# Create feature branch
git checkout -b feature/new-feature

# Make changes and commit
git add .
git commit -m "feat: add new feature"

# Push and create PR
git push origin feature/new-feature
```

### Code Review Checklist
- [ ] PHPStan Level 8 passes
- [ ] All tests pass
- [ ] Code follows PSR-12 standards
- [ ] Frontend follows ESLint rules
- [ ] Security best practices followed
- [ ] Performance considerations addressed

### Deployment Process
1. **Code Review** - Peer review and approval
2. **Testing** - Automated tests pass
3. **Static Analysis** - PHPStan and ESLint pass
4. **Build** - Production build created
5. **Deploy** - Deploy to staging/production
6. **Monitor** - Monitor application health

## 📚 API Documentation

### OpenAPI Specification
The API is documented using OpenAPI 3.0 specification located in `shared/openapi/acme.yaml`.

### Key Endpoints

#### Authentication
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/me` - Get current user

#### Campaigns
- `GET /api/campaigns` - List campaigns
- `GET /api/campaigns/featured` - Featured campaigns
- `GET /api/campaigns/{id}` - Campaign details
- `POST /api/campaigns` - Create campaign
- `PUT /api/campaigns/{id}` - Update campaign

#### Donations
- `POST /api/campaigns/{id}/donations` - Make donation
- `GET /api/donations/my` - User's donations
- `GET /api/donations/{id}/receipt` - Donation receipt

#### Admin
- `GET /api/admin/dashboard` - Admin dashboard
- `POST /api/admin/campaigns/{id}/approve` - Approve campaign
- `POST /api/admin/campaigns/{id}/reject` - Reject campaign

### API Client Generation
```bash
# Generate TypeScript client from OpenAPI spec
docker-compose exec web npm run generate-api-client
```

## 🐳 Docker Configuration

### Services
- **api-php** - PHP-FPM 8.4 with Laravel
- **api-nginx** - Nginx web server
- **web** - Node.js 20 LTS with Vue.js
- **redis** - Redis 7.x for caching
- **queue-worker** - Laravel queue worker

### Environment Variables
```bash
# Database
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Cache & Sessions
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=database

# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8080
```

### Health Checks
- **API Health**: `GET /health`
- **Database**: Connection and migrations status
- **Redis**: Cache and session connectivity
- **Queues**: Queue worker status

## 🚨 Troubleshooting

### Common Issues

#### 502 Bad Gateway Error
If you get a 502 Bad Gateway error, use the robust startup script:

```bash
# Windows (RECOMMENDED)
.\scripts\start-dev.ps1

# Linux/Mac
./scripts/start-dev.sh
```

This script starts services in the correct order and automatically tests the API.

#### Database Read-Only Error
If you get the error `attempt to write a readonly database`:

```bash
# Fix database permissions
.\scripts\dev.ps1 fix-permissions  # Windows
make fix-permissions               # Linux/Mac

# Or manually
docker-compose exec api-php chmod 666 database/database.sqlite
```

#### Queue Worker Issues
```bash
# Restart queue worker
docker-compose restart queue-worker

# Check queue status
docker-compose exec api-php php artisan queue:work --once
```

#### Cache Issues
```bash
# Clear all caches
docker-compose exec api-php php artisan cache:clear
docker-compose exec api-php php artisan config:clear
docker-compose exec api-php php artisan route:clear
```

### Logs
```bash
# View application logs
docker-compose logs -f api-php

# View web server logs
docker-compose logs -f api-nginx

# View queue worker logs
docker-compose logs -f queue-worker
```

## 🤝 Contributing

### Development Setup
1. Fork the repository
2. Clone your fork
3. Create a feature branch
4. Make your changes
5. Run tests and static analysis
6. Submit a pull request

### Code Standards
- **PHP**: PSR-12 coding standard
- **JavaScript/TypeScript**: ESLint configuration
- **CSS**: Tailwind CSS utility classes
- **Git**: Conventional commit messages

### Testing Requirements
- All new features must have tests
- PHPStan Level 8 must pass
- ESLint must pass
- All existing tests must pass

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Laravel** - The PHP framework for web artisans
- **Vue.js** - The progressive JavaScript framework
- **Tailwind CSS** - A utility-first CSS framework
- **Docker** - Containerization platform
- **Redis** - In-memory data structure store

---

**Built with ❤️ for the community**