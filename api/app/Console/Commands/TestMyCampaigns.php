<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Campaign, User};
use App\Http\Controllers\CampaignController;
use Illuminate\Http\Request;

class TestMyCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:my-campaigns {user_id=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test My Campaigns endpoint data';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $userId = $this->argument('user_id');
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User {$userId} not found");
            return;
        }
        
        $this->info("🔍 Testing My Campaigns for user: {$user->name} (ID: {$userId})");
        $this->newLine();
        
        // Test the myCampaigns method directly
        $controller = new CampaignController();
        $request = Request::create('/api/me/campaigns', 'GET');
        $request->setUserResolver(function() use ($user) {
            return $user;
        });
        
        $response = $controller->myCampaigns($request);
        $data = json_decode($response->getContent(), true);
        
        if (empty($data['campaigns'])) {
            $this->warn('No campaigns found for this user');
            return;
        }
        
        $this->info("📊 Found {" . count($data['campaigns']) . "} campaigns:");
        
        foreach ($data['campaigns'] as $campaign) {
            $this->line("  Campaign {$campaign['id']}: {$campaign['title']}");
            $this->line("    Amount: €{$campaign['donated_amount']}");
            $this->line("    Donations: {$campaign['donations_count']}");
            
            // Compare with direct model data
            $model = Campaign::find($campaign['id']);
            $this->line("    [Model] Amount: €{$model->donated_amount}");
            $this->line("    [Model] Total donations: {$model->donations()->count()}");
            $this->line("    [Model] Successful donations: {$model->successfulDonations()->count()}");
            
            if ($campaign['donated_amount'] != $model->donated_amount) {
                $this->error("    ❌ MISMATCH in donated_amount!");
            }
            if ($campaign['donations_count'] != $model->successfulDonations()->count()) {
                $this->error("    ❌ MISMATCH in donations_count!");
            }
            
            $this->newLine();
        }
    }
}
