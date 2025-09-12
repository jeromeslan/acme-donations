<?php

use App\Models\Campaign;
use App\Models\Category;
use App\Models\User;
use App\Models\Donation;
use App\Models\Notification;
use Spatie\Permission\Models\Role;

test('admin can get dashboard statistics', function () {
    Role::create(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    Category::factory()->create();
    Campaign::factory()->active()->count(5)->create();
    Campaign::factory()->pending()->count(3)->create();
    Campaign::factory()->draft()->count(2)->create();
    
    Donation::factory()->completed()->count(10)->create();
    
    $response = $this->actingAs($admin)->get('/api/admin/dashboard');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'campaigns' => [
            'total', 'published', 'pending', 'draft'
        ],
        'donations' => [
            'total_count', 'total_amount'
        ],
        'users' => [
            'total'
        ],
        'recent_campaigns'
    ]);
});

test('admin can get public statistics', function () {
    Category::factory()->create();
    Campaign::factory()->active()->count(5)->create();
    Donation::factory()->completed()->count(10)->create();
    
    $response = $this->get('/api/stats');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'totalRaised',
        'totalCampaigns',
        'totalDonations'
    ]);
});

test('admin can get pending campaigns', function () {
    Role::create(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    Category::factory()->create();
    Campaign::factory()->pending()->count(3)->create();
    Campaign::factory()->active()->count(2)->create();
    
    $response = $this->actingAs($admin)->get('/api/admin/campaigns/pending');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'campaigns' => [
            '*' => [
                'id', 'title', 'description', 'goal_amount', 
                'status', 'featured', 'created_at', 'category', 'creator'
            ]
        ]
    ]);
    
    $campaigns = $response->json('campaigns');
    expect($campaigns)->toHaveCount(3);
    
    foreach ($campaigns as $campaign) {
        expect($campaign['status'])->toBe('pending');
    }
});

test('admin can approve campaign', function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'creator']);
    
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $creator = User::factory()->create();
    $creator->assignRole('creator');
    
    Category::factory()->create();
    $campaign = Campaign::factory()->pending()->create(['creator_id' => $creator->id]);
    
    $response = $this->actingAs($admin)->post("/api/admin/campaigns/{$campaign->id}/approve", [
        'featured' => true
    ]);
    
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Campaign approved successfully'
    ]);
    
    $campaign->refresh();
    expect($campaign->status)->toBe('active');
    expect($campaign->featured)->toBeTrue();
    
    // Check notification was created
    $this->assertDatabaseHas('notifications', [
        'user_id' => $creator->id,
        'campaign_id' => $campaign->id,
        'type' => 'campaign_approved',
    ]);
});

test('admin can reject campaign', function () {
    Role::create(['name' => 'admin']);
    Role::create(['name' => 'creator']);
    
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    $creator = User::factory()->create();
    $creator->assignRole('creator');
    
    Category::factory()->create();
    $campaign = Campaign::factory()->pending()->create(['creator_id' => $creator->id]);
    
    $rejectionReason = 'Content does not meet our guidelines';
    
    $response = $this->actingAs($admin)->post("/api/admin/campaigns/{$campaign->id}/reject", [
        'reason' => $rejectionReason
    ]);
    
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Campaign rejected successfully'
    ]);
    
    $campaign->refresh();
    expect($campaign->status)->toBe('archived');
    expect($campaign->rejection_reason)->toBe($rejectionReason);
    
    // Check notification was created
    $this->assertDatabaseHas('notifications', [
        'user_id' => $creator->id,
        'campaign_id' => $campaign->id,
        'type' => 'campaign_rejected',
    ]);
});

test('admin can get all campaigns', function () {
    Role::create(['name' => 'admin']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    
    Category::factory()->create();
    Campaign::factory()->active()->count(3)->create();
    Campaign::factory()->pending()->count(2)->create();
    Campaign::factory()->draft()->count(1)->create();
    
    $response = $this->actingAs($admin)->get('/api/admin/campaigns/all');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'campaigns' => [
            '*' => [
                'id', 'title', 'description', 'goal_amount', 
                'donated_amount', 'status', 'featured', 'created_at', 
                'category', 'creator', 'donations_count'
            ]
        ]
    ]);
    
    $campaigns = $response->json('campaigns');
    expect($campaigns)->toHaveCount(6); // All campaigns regardless of status
});

test('non-admin cannot access admin endpoints', function () {
    Role::create(['name' => 'creator']);
    $user = User::factory()->create();
    $user->assignRole('creator');
    
    $response = $this->actingAs($user)->get('/api/admin/dashboard');
    
    $response->assertStatus(403);
});

test('unauthenticated user cannot access admin endpoints', function () {
    $response = $this->get('/api/admin/dashboard');
    
    $response->assertStatus(401);
});
