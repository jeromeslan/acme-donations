<?php

require_once __DIR__ . '/vendor/autoload.php';

// Essayer de charger l'application Laravel pour examiner les services
try {
    $app = require_once __DIR__ . '/bootstrap/app.php';

    echo "Application loaded successfully!\n";

    // Examiner les services enregistrés
    $services = $app->getLoadedProviders();
    echo "Loaded providers:\n";
    foreach ($services as $service => $loaded) {
        if (strpos($service, 'Sail') !== false) {
            echo "Found Sail service: $service\n";
        }
    }

    // Examiner les providers différés
    if (method_exists($app, 'getDeferredServices')) {
        $deferred = $app->getDeferredServices();
        echo "Deferred services:\n";
        foreach ($deferred as $service) {
            if (strpos($service, 'Sail') !== false) {
                echo "Found deferred Sail service: $service\n";
            }
        }
    }

} catch (Exception $e) {
    echo "Error loading application: " . $e->getMessage() . "\n";

    // Essayer d'examiner les fichiers de cache directement
    echo "Trying to examine cache files directly...\n";

    $cacheFiles = [
        __DIR__ . '/bootstrap/cache/services.php',
        __DIR__ . '/bootstrap/cache/packages.php',
        __DIR__ . '/bootstrap/cache/config.php'
    ];

    foreach ($cacheFiles as $file) {
        if (file_exists($file)) {
            echo "Examining $file...\n";
            $content = file_get_contents($file);
            if (strpos($content, 'Sail') !== false) {
                echo "Found Sail references in $file\n";

                // Créer une sauvegarde
                copy($file, $file . '.backup');

                // Supprimer les références à Sail
                $newContent = preg_replace('/\'Laravel\\\\Sail\\\\[^\'"]*\'/', "''", $content);
                $newContent = preg_replace('/"Laravel\\\\Sail\\\\[^"]*"/', '""', $newContent);
                file_put_contents($file, $newContent);

                echo "Cleaned Sail references from $file\n";
            }
        } else {
            echo "$file does not exist\n";
        }
    }
}

echo "Script completed.\n";
