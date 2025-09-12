<?php

use App\Models\Donation;
use App\Models\User;
use App\Models\Campaign;

test('donation can be created with factory', function () {
    $donation = Donation::factory()->create();
    
    expect($donation)->toBeInstanceOf(Donation::class);
    expect($donation->amount)->toBeNumeric();
    expect($donation->status)->toBeString();
    expect($donation->payment_method)->toBeString();
    expect($donation->correlation_id)->toBeString();
});

test('donation belongs to user', function () {
    $user = User::factory()->create();
    $donation = Donation::factory()->create(['user_id' => $user->id]);
    
    expect($donation->user)->toBeInstanceOf(User::class);
    expect($donation->user->id)->toBe($user->id);
});

test('donation belongs to campaign', function () {
    $campaign = Campaign::factory()->create();
    $donation = Donation::factory()->create(['campaign_id' => $campaign->id]);
    
    expect($donation->campaign)->toBeInstanceOf(Campaign::class);
    expect($donation->campaign->id)->toBe($campaign->id);
});

test('donation can be created in different states', function () {
    $pending = Donation::factory()->pending()->create();
    $completed = Donation::factory()->completed()->create();
    $failed = Donation::factory()->failed()->create();
    
    expect($pending->status)->toBe('pending');
    expect($pending->processed_at)->toBeNull();
    
    expect($completed->status)->toBe('completed');
    expect($completed->processed_at)->not->toBeNull();
    
    expect($failed->status)->toBe('failed');
    expect($failed->processed_at)->not->toBeNull();
});

test('donation has correct fillable attributes', function () {
    $donation = new Donation();
    
    expect($donation->getFillable())->toContain(
        'amount', 'user_id', 'campaign_id', 'status', 
        'payment_reference', 'correlation_id'
    );
});

test('donation correlation_id is unique', function () {
    $donation1 = Donation::factory()->create();
    $donation2 = Donation::factory()->create();
    
    expect($donation1->correlation_id)->not->toBe($donation2->correlation_id);
    expect($donation1->correlation_id)->toHaveLength(36); // UUID length
});

test('donation amount is positive', function () {
    $donation = Donation::factory()->create(['amount' => 100]);
    
    expect($donation->amount)->toBeGreaterThan(0);
});
