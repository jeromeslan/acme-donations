<?php

require_once __DIR__ . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use App\Jobs\ProcessDonationJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

echo "🧪 Test de création de donation...\n";

// Récupérer un utilisateur et une campagne
$user = User::first();
$campaign = Campaign::where('status', 'active')->first();

if (!$user || !$campaign) {
    echo "❌ Pas d'utilisateur ou de campagne active trouvée\n";
    exit(1);
}

echo "👤 Utilisateur: {$user->name} (ID: {$user->id})\n";
echo "📋 Campagne: {$campaign->title} (ID: {$campaign->id})\n";
echo "💰 Montant actuel: {$campaign->donated_amount}€\n";

// Créer une donation
$donation = Donation::create([
    'campaign_id' => $campaign->id,
    'user_id' => $user->id,
    'amount' => 25.50,
    'status' => 'processing',
    'correlation_id' => (string) Str::uuid(),
]);

echo "✅ Donation créée (ID: {$donation->id})\n";

// Dispatcher le job
try {
    $job = new ProcessDonationJob($donation->id);
    Bus::dispatch($job->onQueue('donations'));
    echo "✅ Job dispatché vers la queue 'donations'\n";
} catch (Exception $e) {
    echo "❌ Erreur lors du dispatch: " . $e->getMessage() . "\n";
    exit(1);
}

echo "🎯 Test terminé ! Vérifiez les logs du queue-worker.\n";
