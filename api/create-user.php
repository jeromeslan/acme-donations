<?php

require_once __DIR__ . '/vendor/autoload.php';

try {
    $app = require_once __DIR__ . '/bootstrap/app.php';

    // Créer un utilisateur de test
    $user = new \App\Models\User();
    $user->name = 'Admin';
    $user->email = 'admin@example.com';
    $user->password = bcrypt('password');
    $user->email_verified_at = now();
    $user->save();

    echo "✅ Utilisateur créé avec succès !\n";
    echo "Email: admin@example.com\n";
    echo "Mot de passe: password\n";

} catch (Exception $e) {
    echo "❌ Erreur lors de la création de l'utilisateur: " . $e->getMessage() . "\n";
}
