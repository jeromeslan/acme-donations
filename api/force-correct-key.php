<?php

echo "🔧 Forçage de la clé correcte...\n";

// Forcer la bonne clé dans l'environnement
putenv('APP_KEY=base64:YJgmtLJEpaemj5Z8QuK0OxXQ4bwzakaq5dqganLW8BM=');

echo "APP_KEY défini: " . getenv('APP_KEY') . "\n";

// Charger Laravel
require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Application Laravel chargée\n";

    // Créer un nouvel encrypter avec la bonne clé
    $correctKey = 'base64:YJgmtLJEpaemj5Z8QuK0OxXQ4bwzakaq5dqganLW8BM=';
    $encrypter = new \Illuminate\Encryption\Encrypter($correctKey, 'AES-256-CBC');

    // Remplacer l'encrypter dans le conteneur
    $app->singleton('encrypter', function () use ($encrypter) {
        return $encrypter;
    });

    echo "✅ Encrypter remplacé avec la clé correcte\n";

    // Tester Sanctum
    $request = \Illuminate\Http\Request::create('/sanctum/csrf-cookie', 'GET');
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

    try {
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();

        if ($status === 200 || $status === 204) {
            echo "🎉 SUCCÈS ! Sanctum fonctionne (status: $status)\n";

            // Vérifier les cookies
            $cookies = $response->headers->getCookies();
            if (!empty($cookies)) {
                echo "✅ Cookies présents:\n";
                foreach ($cookies as $cookie) {
                    echo "   - {$cookie->getName()}\n";
                }
            }
        } else {
            echo "⚠️ Status inattendu: $status\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur lors du chargement: " . $e->getMessage() . "\n";
}

echo "\n🏁 Terminé.\n";
