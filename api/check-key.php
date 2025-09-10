<?php

echo "=== VÉRIFICATION DE LA CLÉ ===\n";

// Vérifier la clé depuis les variables d'environnement
echo "APP_KEY from getenv(): " . getenv('APP_KEY') . "\n";

// Essayer de charger Laravel et vérifier la config
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "Application loaded successfully\n";

    // Vérifier la clé depuis la config
    $key = config('app.key');
    echo "APP_KEY from config(): " . $key . "\n";

    // Tester l'encrypter
    if ($key) {
        $encrypter = new \Illuminate\Encryption\Encrypter($key, config('app.cipher', 'AES-256-CBC'));
        echo "✅ Encrypter created successfully with key: " . substr($key, 0, 20) . "...\n";
    } else {
        echo "❌ No APP_KEY in config\n";
    }
} catch (Exception $e) {
    echo "❌ Error loading application: " . $e->getMessage() . "\n";
}

echo "=== FIN ===";
