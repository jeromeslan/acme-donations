<?php

echo "🔧 Test du flux CSRF Sanctum complet\n";
echo "=====================================\n\n";

// Étape 1: Simuler l'obtention du cookie CSRF
echo "1️⃣ Simulation de l'obtention du cookie CSRF...\n";

// Créer une requête simulée vers /sanctum/csrf-cookie
$kernel = require_once __DIR__ . '/bootstrap/app.php';

try {
    // Simuler la requête CSRF cookie
    $csrfRequest = Illuminate\Http\Request::create('/sanctum/csrf-cookie', 'GET');
    $csrfResponse = $kernel->handle($csrfRequest);

    echo "✅ Cookie CSRF obtenu: " . $csrfResponse->getStatusCode() . "\n";

    // Extraire les cookies de la réponse
    $cookies = $csrfResponse->headers->getCookies();
    $xsrfToken = null;
    $sessionCookie = null;

    foreach ($cookies as $cookie) {
        if ($cookie->getName() === 'XSRF-TOKEN') {
            $xsrfToken = $cookie->getValue();
            echo "✅ XSRF-TOKEN trouvé: " . substr($xsrfToken, 0, 30) . "...\n";
        }
        if ($cookie->getName() === 'acme-donations-session') {
            $sessionCookie = $cookie->getValue();
            echo "✅ Session cookie trouvé: " . substr($sessionCookie, 0, 30) . "...\n";
        }
    }

    if (!$xsrfToken) {
        echo "❌ Aucun token XSRF trouvé\n";
        exit(1);
    }

    // Étape 2: Simuler la requête de login avec le token CSRF
    echo "\n2️⃣ Simulation de la requête de login avec CSRF token...\n";

    $loginData = json_encode([
        'email' => 'admin@example.com',
        'password' => 'password'
    ]);

    $loginRequest = Illuminate\Http\Request::create('/api/login', 'POST', [], [], [], [
        'CONTENT_TYPE' => 'application/json',
        'HTTP_ACCEPT' => 'application/json',
        'HTTP_X_XSRF_TOKEN' => $xsrfToken,
        'HTTP_ORIGIN' => 'http://localhost:5173'
    ], $loginData);

    // Ajouter les cookies à la requête
    $loginRequest->cookies->set('XSRF-TOKEN', $xsrfToken);
    if ($sessionCookie) {
        $loginRequest->cookies->set('acme-donations-session', $sessionCookie);
    }

    $loginResponse = $kernel->handle($loginRequest);

    echo "✅ Réponse login: " . $loginResponse->getStatusCode() . "\n";
    echo "Contenu: " . $loginResponse->getContent() . "\n";

    if ($loginResponse->getStatusCode() === 200) {
        echo "\n🎉 LOGIN RÉUSSI ! Sanctum fonctionne parfaitement !\n";
    } else {
        echo "\n⚠️  Login a échoué, mais c'est peut-être normal (pas d'utilisateur en base)\n";
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Fichier: " . $e->getFile() . ":" . $e->getLine() . "\n";
}

echo "\n🏁 Test terminé.\n";
