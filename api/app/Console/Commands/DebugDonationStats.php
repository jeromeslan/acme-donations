<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Campaign, Donation};
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\CampaignController;
use Illuminate\Http\Request;

class DebugDonationStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'debug:donation-stats {campaign_id?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Debug donation statistics update issues';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->info('🔍 Debugging donation stats issue...');
        $this->newLine();

        $campaignId = $this->argument('campaign_id');
        
        if ($campaignId) {
            $campaign = Campaign::with('donations')->find($campaignId);
        } else {
            // Get the latest campaign with donations
            $campaign = Campaign::with('donations')->orderByDesc('id')->first();
        }

        if (!$campaign) {
            $this->error('❌ No campaigns found');
            return;
        }

        $this->info("📊 Campaign: {$campaign->title} (ID: {$campaign->id})");
        $this->info("👤 Creator ID: {$campaign->creator_id}");
        $this->info("💰 donated_amount (from campaign): {$campaign->donated_amount}");

        // Calculate sum from donations
        $completedDonations = $campaign->donations->whereIn('status', ['completed', 'succeeded']);
        $actualSum = $completedDonations->sum('amount');
        $this->info("💰 actual sum from completed/succeeded donations: {$actualSum}");
        $this->info("📊 completed/succeeded donations count: {$completedDonations->count()}");

        // Check cache
        $cacheKey = "campaigns:show:{$campaign->id}";
        $cachedData = Cache::store('redis')->tags(["campaign:{$campaign->id}", 'campaigns'])->get($cacheKey);
        if ($cachedData) {
            $this->info("📦 Cached donated_amount: {$cachedData->donated_amount}");
        } else {
            $this->info("📦 No cached data found");
        }

        // Fresh fetch from database
        $freshCampaign = Campaign::find($campaign->id);
        $this->info("🔄 Fresh from DB donated_amount: {$freshCampaign->donated_amount}");

        // List recent donations
        $this->newLine();
        $this->info("📋 Recent donations for this campaign:");
        $recentDonations = $campaign->donations->sortByDesc('id')->take(5);
        
        if ($recentDonations->isEmpty()) {
            $this->warn("  No donations found for this campaign");
        } else {
            foreach ($recentDonations as $donation) {
                $processed = $donation->processed_at ? $donation->processed_at->format('Y-m-d H:i:s') : 'null';
                $this->line("  - ID: {$donation->id}, Amount: {$donation->amount}, Status: {$donation->status}, Processed: {$processed}");
            }
        }

        // Test API endpoint response
        $this->newLine();
        $this->info("🌐 Testing API endpoint response...");
        try {
            $controller = new CampaignController();
            $response = $controller->show($campaign);
            $data = json_decode($response->getContent(), true);
            $this->info("📡 API response donated_amount: {$data['donated_amount']}");
            $this->info("📊 API response donations_count: {$data['donations_count']}");
        } catch (\Exception $e) {
            $this->error("❌ API test failed: {$e->getMessage()}");
        }

        // Check for discrepancies
        $this->newLine();
        if ($actualSum != $campaign->donated_amount) {
            $this->error("⚠️  DISCREPANCY FOUND!");
            $this->error("   DB donated_amount: {$campaign->donated_amount}");
            $this->error("   Calculated sum: {$actualSum}");
            $this->error("   Difference: " . ($actualSum - $campaign->donated_amount));
        } else {
            $this->info("✅ No discrepancies found - amounts match");
        }

        // Debug campaign status and visibility
        $this->newLine();
        $this->info("🔎 Campaign visibility debug:");
        $this->line("   Status: {$campaign->status}");
        $this->line("   Featured: " . ($campaign->featured ? 'Yes' : 'No'));
        
        // Check donations by status
        $allDonations = $campaign->donations;
        $statusCounts = [];
        foreach ($allDonations as $donation) {
            $status = $donation->status;
            $statusCounts[$status] = ($statusCounts[$status] ?? 0) + 1;
        }
        
        $this->info("📊 Donations by status:");
        foreach ($statusCounts as $status => $count) {
            $sum = $campaign->donations->where('status', $status)->sum('amount');
            $this->line("   - {$status}: {$count} donations, sum: {$sum}");
        }

        // Test if campaign appears in index
        $this->info("🔍 Index visibility test:");
        $indexQuery = Campaign::query()
            ->with('category')
            ->withCount('successfulDonations as donations_count')
            ->where('status', 'active')
            ->orderByDesc('id');
        
        $activeCampaigns = $indexQuery->get();
        $foundInIndex = $activeCampaigns->firstWhere('id', $campaign->id);
        
        $this->line("   Total active campaigns: {$activeCampaigns->count()}");
        $this->line("   This campaign in index: " . ($foundInIndex ? 'YES' : 'NO'));
        
        if (!$foundInIndex && $campaign->status === 'active') {
            $this->error("   ❌ Campaign has active status but not found in index!");
        }
    }
}
