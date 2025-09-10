<?php

// Test direct de Sanctum
require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';

    echo "✅ Application Laravel chargée avec succès !\n";

    // Tester si Sanctum est disponible
    if ($app->providerIsLoaded('Laravel\Sanctum\SanctumServiceProvider')) {
        echo "✅ SanctumServiceProvider est chargé !\n";
    } else {
        echo "❌ SanctumServiceProvider n'est pas chargé\n";
    }

    // Tester les routes
    $routes = $app->router->getRoutes();
    $sanctumRoutes = [];
    foreach ($routes as $route) {
        if (strpos($route->uri(), 'sanctum') !== false) {
            $sanctumRoutes[] = $route->uri();
        }
    }

    if (!empty($sanctumRoutes)) {
        echo "✅ Routes Sanctum trouvées :\n";
        foreach ($sanctumRoutes as $route) {
            echo "  - $route\n";
        }
    } else {
        echo "❌ Aucune route Sanctum trouvée\n";
    }

    // Tester une requête HTTP simulée
    echo "\n🔍 Test d'une requête HTTP simulée...\n";

    $request = \Illuminate\Http\Request::create('/sanctum/csrf-cookie', 'GET');
    $kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

    try {
        $response = $kernel->handle($request);
        echo "✅ Requête traitée avec succès ! Status: " . $response->getStatusCode() . "\n";

        if ($response->getStatusCode() === 200) {
            echo "🎉 Sanctum fonctionne correctement !\n";
        } else {
            echo "⚠️  Status inattendu: " . $response->getStatusCode() . "\n";
            echo "Contenu: " . substr($response->getContent(), 0, 200) . "...\n";
        }
    } catch (Exception $e) {
        echo "❌ Erreur lors du traitement de la requête: " . $e->getMessage() . "\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur lors du chargement de l'application: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n🏁 Test terminé.\n";
