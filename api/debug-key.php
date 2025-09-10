<?php

// Debug script pour comprendre le problème de clé
require_once __DIR__ . '/vendor/autoload.php';

echo "=== DEBUG KEY ===\n";

// Lire directement le fichier .env
$envFile = __DIR__ . '/.env';
if (file_exists($envFile)) {
    $envContent = file_get_contents($envFile);
    if (preg_match('/APP_KEY=(.+)/', $envContent, $matches)) {
        echo "APP_KEY from .env: " . $matches[1] . "\n";
    } else {
        echo "APP_KEY not found in .env\n";
    }
}

// Tester la fonction env() de Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';

echo "APP_KEY from Laravel env(): " . env('APP_KEY') . "\n";
echo "APP_KEY from config(): " . config('app.key') . "\n";

// Tester l'encrypter directement
try {
    $key = config('app.key');
    if ($key) {
        $encrypter = new \Illuminate\Encryption\Encrypter($key, config('app.cipher', 'AES-256-CBC'));
        echo "✅ Encrypter created successfully!\n";
        echo "Cipher: " . $encrypter->getKey() . "\n";
    } else {
        echo "❌ No APP_KEY found\n";
    }
} catch (Exception $e) {
    echo "❌ Error creating encrypter: " . $e->getMessage() . "\n";
}

echo "=== END DEBUG ===\n";
