<?php

use App\Models\Campaign;
use App\Models\User;
use App\Models\Category;
use App\Models\Donation;

test('campaign can be created with factory', function () {
    $campaign = Campaign::factory()->create();
    
    expect($campaign)->toBeInstanceOf(Campaign::class);
    expect($campaign->title)->toBeString();
    expect($campaign->description)->toBeString();
    expect($campaign->goal_amount)->toBeNumeric();
    expect($campaign->status)->toBeString();
});

test('campaign belongs to creator', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create(['creator_id' => $user->id]);
    
    expect($campaign->creator)->toBeInstanceOf(User::class);
    expect($campaign->creator->id)->toBe($user->id);
});

test('campaign belongs to category', function () {
    $category = Category::factory()->create();
    $campaign = Campaign::factory()->create(['category_id' => $category->id]);
    
    expect($campaign->category)->toBeInstanceOf(Category::class);
    expect($campaign->category->id)->toBe($category->id);
});

test('campaign has donations relationship', function () {
    $campaign = Campaign::factory()->create();
    $donation = Donation::factory()->create(['campaign_id' => $campaign->id]);
    
    expect($campaign->donations)->toHaveCount(1);
    expect($campaign->donations->first())->toBeInstanceOf(Donation::class);
    expect($campaign->donations->first()->campaign_id)->toBe($campaign->id);
});

test('campaign can be created in different states', function () {
    $draft = Campaign::factory()->draft()->create();
    $pending = Campaign::factory()->pending()->create();
    $active = Campaign::factory()->active()->create();
    $completed = Campaign::factory()->completed()->create();
    
    expect($draft->status)->toBe('draft');
    expect($pending->status)->toBe('pending');
    expect($active->status)->toBe('active');
    expect($completed->status)->toBe('completed');
});

test('campaign can be featured', function () {
    $featured = Campaign::factory()->featured()->create();
    
    expect($featured->featured)->toBeTrue();
    expect($featured->status)->toBe('active');
});

test('campaign progress percentage is calculated correctly', function () {
    $campaign = Campaign::factory()->create([
        'goal_amount' => 1000,
        'donated_amount' => 250
    ]);
    
    $progress = ($campaign->donated_amount / $campaign->goal_amount) * 100;
    
    expect($progress)->toBe(25.0);
});

test('campaign is fillable with correct attributes', function () {
    $data = [
        'title' => 'Test Campaign',
        'description' => 'Test Description',
        'goal_amount' => 5000,
        'donated_amount' => 0,
        'status' => 'draft',
        'featured' => false,
        'category_id' => 1,
        'creator_id' => 1,
    ];
    
    $campaign = new Campaign($data);
    
    expect($campaign->getFillable())->toContain('title', 'description', 'goal_amount', 'status', 'featured');
});
