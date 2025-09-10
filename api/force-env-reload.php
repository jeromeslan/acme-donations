<?php

echo "🔧 Forçage de la relecture du fichier .env...\n";

// Lire le fichier .env directement et définir les variables d'environnement
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);

            // Définir la variable d'environnement
            putenv("$key=$value");

            if ($key === 'APP_KEY') {
                echo "✅ Défini APP_KEY: " . substr($value, 0, 30) . "...\n";
            }
        }
    }
}

// Vérifier que la variable est bien définie
echo "getenv('APP_KEY'): " . getenv('APP_KEY') . "\n";

// Maintenant charger Laravel
require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Application Laravel chargée avec succès\n";

    // Tester l'encrypter
    $key = config('app.key');
    echo "config('app.key'): " . substr($key, 0, 30) . "...\n";

    if ($key && $key !== 'base64:your-app-key-here') {
        $encrypter = new \Illuminate\Encryption\Encrypter($key, config('app.cipher', 'AES-256-CBC'));
        echo "✅ Encrypter créé avec succès !\n";

        // Tester Sanctum
        $providers = $app->getLoadedProviders();
        if (isset($providers['Laravel\Sanctum\SanctumServiceProvider'])) {
            echo "✅ SanctumServiceProvider chargé\n";
        } else {
            echo "❌ SanctumServiceProvider pas chargé\n";
        }
    } else {
        echo "❌ Clé invalide ou manquante\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur lors du chargement de l'application: " . $e->getMessage() . "\n";
}

echo "\n🏁 Terminé.\n";