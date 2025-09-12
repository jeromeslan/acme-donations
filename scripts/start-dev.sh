#!/bin/bash
# Robust startup script to avoid 502 Bad Gateway errors
# Usage: ./scripts/start-dev.sh

echo "🚀 Starting development environment..."

# Stop all existing services
echo "⏹️  Stopping existing services..."
docker-compose down

# Start services in order
echo "🔄 Starting services..."
docker-compose up -d redis
sleep 2

docker-compose up -d api-php
sleep 5

docker-compose up -d api-nginx
sleep 2

docker-compose up -d queue-worker
sleep 2

docker-compose up -d web

# Wait for all services to be ready
echo "⏳ Waiting for all services to be ready..."
sleep 10

# Install frontend dependencies and start dev server
echo "📦 Installing frontend dependencies..."
docker-compose exec web npm install

# Fix SQLite database permissions
echo "🔧 Fixing database permissions..."
docker-compose exec api-php chmod 666 database/database.sqlite

# Run database migrations
echo "📊 Running database migrations..."
docker-compose exec api-php php artisan migrate --force

# Seed the database with demo data
echo "🌱 Seeding database with demo data..."
docker-compose exec api-php php artisan db:seed --force

# Create demo users
echo "👥 Creating demo users..."
docker-compose exec api-php php artisan db:seed --class=UserSeeder

# Check services status
echo "🔍 Checking services status..."
docker-compose ps

# Test API
echo "🧪 Testing API..."
if curl -s -f http://localhost:8080/health > /dev/null; then
    echo "✅ API is working correctly!"
else
    echo "❌ Error testing API"
    echo "🔄 Restarting API services..."
    docker-compose restart api-php api-nginx
    sleep 5
    
    # Retry test
    if curl -s -f http://localhost:8080/health > /dev/null; then
        echo "✅ API is working after restart!"
    else
        echo "❌ API is still not working. Check logs with: docker-compose logs api-php"
    fi
fi

echo "🎉 Development environment ready!"
echo "📱 Frontend: http://localhost:5173"
echo "🔧 API: http://localhost:8080"
echo "📊 Redis: localhost:6379"
echo ""
echo "👥 Demo accounts created:"
echo "   Admin: admin@acme.test / password"
echo "   User: user@acme.test / password"
echo "   Creator: creator@acme.test / password"
