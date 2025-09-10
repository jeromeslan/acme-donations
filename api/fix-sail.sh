#!/bin/bash

echo "🔧 Correction des références à Laravel Sail..."

# Supprimer tous les fichiers de cache
rm -rf bootstrap/cache/*.php
rm -rf storage/framework/cache/data/*
rm -rf storage/framework/views/*
rm -rf storage/framework/sessions/*

# Forcer la régénération des caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Créer les répertoires de cache s'ils n'existent pas
mkdir -p bootstrap/cache
mkdir -p storage/framework/cache/data
mkdir -p storage/framework/views
mkdir -p storage/framework/sessions

echo "✅ Nettoyage terminé. Les caches seront régénérés au prochain redémarrage."
