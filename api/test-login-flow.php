<?php

// Test final du flux de login Sanctum
require_once __DIR__ . '/vendor/autoload.php';

echo "🚀 Test du flux de login Sanctum\n";
echo "================================\n";

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';
    echo "✅ Application Laravel chargée\n";

    // Vérifier si Sanctum est chargé
    $providers = $app->getLoadedProviders();
    $sanctumLoaded = false;
    foreach ($providers as $provider => $loaded) {
        if (strpos($provider, 'Sanctum') !== false) {
            $sanctumLoaded = true;
            echo "✅ SanctumServiceProvider chargé\n";
            break;
        }
    }

    if (!$sanctumLoaded) {
        echo "❌ SanctumServiceProvider pas chargé\n";
    }

    // Tester les routes Sanctum
    $routes = $app->router->getRoutes();
    $sanctumRoutes = [];
    foreach ($routes as $route) {
        $uri = $route->uri();
        if (strpos($uri, 'sanctum') !== false) {
            $sanctumRoutes[] = $uri;
        }
    }

    if (!empty($sanctumRoutes)) {
        echo "✅ Routes Sanctum trouvées:\n";
        foreach ($sanctumRoutes as $route) {
            echo "   - $route\n";
        }
    } else {
        echo "❌ Aucune route Sanctum trouvée\n";
    }

    // Tester la route CSRF cookie
    echo "\n🔍 Test de la route CSRF cookie...\n";

    $request = \Illuminate\Http\Request::create('/sanctum/csrf-cookie', 'GET');
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

    try {
        $response = $kernel->handle($request);
        $status = $response->getStatusCode();

        if ($status === 200) {
            echo "🎉 SUCCÈS ! Route CSRF cookie fonctionne (status: $status)\n";

            // Vérifier les cookies dans la réponse
            $cookies = $response->headers->getCookies();
            if (!empty($cookies)) {
                echo "✅ Cookies présents dans la réponse:\n";
                foreach ($cookies as $cookie) {
                    echo "   - {$cookie->getName()}\n";
                }
            } else {
                echo "⚠️  Aucun cookie dans la réponse\n";
            }

            echo "\n🎊 Sanctum est maintenant opérationnel !\n";
            echo "Le frontend peut maintenant utiliser l'authentification SPA.\n";

        } elseif ($status === 204) {
            echo "🎉 SUCCÈS ! Route CSRF cookie fonctionne (status: $status - No Content)\n";
            echo "✅ Sanctum est opérationnel !\n";
        } else {
            echo "⚠️  Status inattendu: $status\n";
            echo "Contenu: " . substr($response->getContent(), 0, 200) . "...\n";
        }

    } catch (Exception $e) {
        echo "❌ Erreur lors du traitement: " . $e->getMessage() . "\n";
        echo "Type: " . get_class($e) . "\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur générale: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🏁 Test terminé.\n";
