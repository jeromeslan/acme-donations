<?php

use App\Models\Campaign;
use App\Models\Category;
use App\Models\User;
use App\Models\Donation;
use Spatie\Permission\Models\Role;

test('can list campaigns without authentication', function () {
    Category::factory()->create();
    Campaign::factory()->active()->count(3)->create();
    
    $response = $this->get('/api/campaigns');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id', 'title', 'description', 'goal_amount', 
                'donated_amount', 'status', 'featured', 'category'
            ]
        ]
    ]);
});

test('can get featured campaigns', function () {
    $category = Category::factory()->create();
    
    // Create exactly 2 featured campaigns  
    Campaign::factory()->featured()->count(2)->create(['category_id' => $category->id]);
    // Create 3 active but not featured campaigns
    Campaign::factory()->active()->count(3)->create(['category_id' => $category->id, 'featured' => false]);
    
    $response = $this->get('/api/campaigns/featured');
    
    $response->assertStatus(200);
    
    $featuredCampaigns = $response->json();
    expect($featuredCampaigns)->toHaveCount(2);
    
    foreach ($featuredCampaigns as $campaign) {
        expect($campaign['featured'])->toBeTrue();
        expect($campaign['status'])->toBe('active');
    }
});

test('can show individual campaign', function () {
    $category = Category::factory()->create();
    $campaign = Campaign::factory()->active()->create(['category_id' => $category->id]);
    
    $response = $this->get("/api/campaigns/{$campaign->id}");
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'id',
        'title',
        'description', 
        'goal_amount',
        'donated_amount',
        'status',
        'featured',
        'category' => [
            'id',
            'name',
            'slug'
        ]
    ]);
    
    // Verify the campaign ID matches
    $responseData = $response->json();
    expect($responseData['id'])->toBe($campaign->id);
    expect($responseData['status'])->toBe('active');
});

test('can filter campaigns by status', function () {
    Category::factory()->create();
    Campaign::factory()->active()->count(2)->create();
    Campaign::factory()->pending()->count(3)->create();
    
    $response = $this->get('/api/campaigns?status=active');
    
    $response->assertStatus(200);
    $campaigns = $response->json('data');
    
    expect($campaigns)->toHaveCount(2);
    foreach ($campaigns as $campaign) {
        expect($campaign['status'])->toBe('active');
    }
});

test('can search campaigns by title', function () {
    Category::factory()->create();
    Campaign::factory()->active()->create(['title' => 'Education Fund']);
    Campaign::factory()->active()->create(['title' => 'Healthcare Support']);
    Campaign::factory()->active()->create(['title' => 'Environmental Project']);
    
    $response = $this->get('/api/campaigns?q=Education');
    
    $response->assertStatus(200);
    $campaigns = $response->json('data');
    
    expect($campaigns)->toHaveCount(1);
    expect($campaigns[0]['title'])->toBe('Education Fund');
});

test('can filter campaigns by category', function () {
    $educationCategory = Category::factory()->create(['name' => 'Education']);
    $healthCategory = Category::factory()->create(['name' => 'Healthcare']);
    
    Campaign::factory()->active()->create(['category_id' => $educationCategory->id]);
    Campaign::factory()->active()->create(['category_id' => $healthCategory->id]);
    
    $response = $this->get("/api/campaigns?category_id={$educationCategory->id}");
    
    $response->assertStatus(200);
    $campaigns = $response->json('data');
    
    expect($campaigns)->toHaveCount(1);
    expect($campaigns[0]['category']['id'])->toBe($educationCategory->id);
});

test('authenticated user can create campaign', function () {
    Role::create(['name' => 'creator']);
    $user = User::factory()->create();
    $user->assignRole('creator');
    
    $category = Category::factory()->create();
    
    $campaignData = [
        'title' => 'Test Campaign',
        'description' => 'This is a test campaign',
        'goal_amount' => 5000,
        'category_id' => $category->id,
    ];
    
    $response = $this->actingAs($user)->post('/api/campaigns', $campaignData);
    
    $response->assertStatus(201);
    $response->assertJson([
        'title' => 'Test Campaign',
        'description' => 'This is a test campaign',
        'goal_amount' => '5000.00',
        'status' => 'pending',
        'creator_id' => $user->id,
    ]);
    
    $this->assertDatabaseHas('campaigns', [
        'title' => 'Test Campaign',
        'creator_id' => $user->id,
    ]);
});

test('unauthenticated user cannot create campaign', function () {
    $category = Category::factory()->create();
    
    $campaignData = [
        'title' => 'Test Campaign',
        'description' => 'This is a test campaign',
        'goal_amount' => 5000,
        'category_id' => $category->id,
    ];
    
    $response = $this->postJson('/api/campaigns', $campaignData);
    
    $response->assertStatus(401);
});

test('user can get their own campaigns', function () {
    Role::create(['name' => 'creator']);
    $user = User::factory()->create();
    $user->assignRole('creator');
    
    Category::factory()->create();
    Campaign::factory()->create(['creator_id' => $user->id]);
    Campaign::factory()->create(['creator_id' => $user->id]);
    Campaign::factory()->create(); // Different user's campaign
    
    $response = $this->actingAs($user)->get('/api/me/campaigns');
    
    $response->assertStatus(200);
    $campaigns = $response->json('campaigns');
    
    expect($campaigns)->toHaveCount(2);
    foreach ($campaigns as $campaign) {
        expect($campaign['creator_id'])->toBe($user->id);
    }
});

test('campaign shows correct donation count', function () {
    $category = Category::factory()->create();
    $campaign = Campaign::factory()->active()->create(['category_id' => $category->id]);
    
    Donation::factory()->completed()->count(3)->create(['campaign_id' => $campaign->id]);
    
    $response = $this->get("/api/campaigns/{$campaign->id}");
    
    $response->assertStatus(200);
    $response->assertJson([
        'donations_count' => 3,
    ]);
});
