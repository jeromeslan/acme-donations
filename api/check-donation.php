<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Campaign;
use App\Models\Donation;

$campaign = Campaign::find(1);
$donation = Donation::find(484);

echo "📋 Campaign #1:\n";
echo "   Montant: {$campaign->donated_amount}€\n";
echo "   Donations count: " . $campaign->donations()->count() . "\n";

echo "\n💰 Donation #484:\n";
echo "   Status: {$donation->status}\n";
echo "   Amount: {$donation->amount}€\n";

// Vérifier s'il y a des jobs en attente
$jobsCount = \DB::table('jobs')->count();
echo "\n📋 Jobs en attente: {$jobsCount}\n";
