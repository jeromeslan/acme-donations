<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Laravel\Sanctum\SanctumServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

// Test simple de Sanctum
echo "Testing Sanctum...\n";

try {
    // Créer une application Laravel minimale
    $app = new Application(__DIR__ . '/..');

    // Enregistrer Sanctum
    $app->register(SanctumServiceProvider::class);

    echo "Sanctum registered successfully!\n";

    // Tester la création d'une route Sanctum
    $kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

    echo "Kernel created successfully!\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString() . "\n";
}

echo "Test completed.\n";
