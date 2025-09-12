#!/bin/bash
# Script de démarrage robuste pour éviter les 502 Bad Gateway
# Usage: ./scripts/start-dev.sh

echo "🚀 Démarrage de l'environnement de développement..."

# Arrêter tous les services existants
echo "⏹️  Arrêt des services existants..."
docker-compose down

# Démarrer les services dans l'ordre
echo "🔄 Démarrage des services..."
docker-compose up -d redis
sleep 2

docker-compose up -d api-php
sleep 5

docker-compose up -d api-nginx
sleep 2

docker-compose up -d queue-worker
sleep 2

docker-compose up -d web

# Attendre que tous les services soient prêts
echo "⏳ Attente que tous les services soient prêts..."
sleep 10

# Corriger les permissions de la base de données SQLite
echo "🔧 Correction des permissions de la base de données..."
docker-compose exec api-php chmod 666 database/database.sqlite

# Vérifier l'état des services
echo "🔍 Vérification de l'état des services..."
docker-compose ps

# Tester l'API
echo "🧪 Test de l'API..."
if curl -s -f http://localhost:8080/api/stats > /dev/null; then
    echo "✅ API fonctionne correctement !"
else
    echo "❌ Erreur lors du test de l'API"
    echo "🔄 Redémarrage des services API..."
    docker-compose restart api-php api-nginx
    sleep 5
    
    # Nouveau test
    if curl -s -f http://localhost:8080/api/stats > /dev/null; then
        echo "✅ API fonctionne après redémarrage !"
    else
        echo "❌ L'API ne fonctionne toujours pas. Vérifiez les logs avec: docker-compose logs api-php"
    fi
fi

echo "🎉 Environnement de développement prêt !"
echo "📱 Frontend: http://localhost:5173"
echo "🔧 API: http://localhost:8080"
echo "📊 Redis: localhost:6379"
