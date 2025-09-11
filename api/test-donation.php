<?php

require_once 'bootstrap/app.php';

$app = require_once 'bootstrap/app.php';

// Boot the application
$app->boot();

use App\Models\User;
use App\Models\Campaign;
use App\Models\Donation;
use App\Jobs\ProcessDonationJob;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Str;

echo "🧪 Testing donation workflow...\n";

try {
    // Get first user and active campaign
    $user = User::first();
    $campaign = Campaign::where('status', 'active')->first();
    
    if (!$user || !$campaign) {
        echo "❌ No user or active campaign found\n";
        exit(1);
    }
    
    echo "👤 User: {$user->name} (ID: {$user->id})\n";
    echo "🎯 Campaign: {$campaign->title} (ID: {$campaign->id})\n";
    echo "💰 Current amount: €{$campaign->donated_amount}\n";
    
    // Create donation
    $donation = Donation::create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'amount' => 25.50,
        'status' => 'processing',
        'correlation_id' => (string) Str::uuid(),
    ]);
    
    echo "✅ Donation created with ID: {$donation->id}\n";
    
    // Dispatch job
    $job = new ProcessDonationJob($donation->id);
    $result = Bus::dispatch($job->onQueue('donations'));
    
    echo "🚀 Job dispatched successfully\n";
    echo "📊 Job result: " . var_export($result, true) . "\n";
    
    // Check if job was queued
    $jobsCount = DB::table('jobs')->count();
    echo "📋 Jobs in queue: {$jobsCount}\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "📍 File: " . $e->getFile() . ":" . $e->getLine() . "\n";
}
