<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Category;
use App\Models\Donation;
use App\Models\Notification;

test('user model has correct fillable attributes', function () {
    $user = new User();
    
    expect($user->getFillable())->toContain('name', 'email', 'password');
});

test('campaign model has correct fillable attributes', function () {
    $campaign = new Campaign();
    
    expect($campaign->getFillable())->toContain(
        'title', 'description', 'goal_amount', 'donated_amount', 
        'status', 'featured', 'category_id', 'creator_id'
    );
});

test('category model has correct fillable attributes', function () {
    $category = new Category();
    
    expect($category->getFillable())->toContain('name', 'slug');
});

test('donation model has correct fillable attributes', function () {
    $donation = new Donation();
    
    expect($donation->getFillable())->toContain(
        'campaign_id', 'user_id', 'amount', 'status', 
        'payment_reference', 'correlation_id'
    );
});

test('notification model has correct fillable attributes', function () {
    $notification = new Notification();
    
    expect($notification->getFillable())->toContain(
        'user_id', 'campaign_id', 'type', 'title', 'message', 'data', 'read_at'
    );
});

test('campaign progress calculation works correctly', function () {
    $campaign = new Campaign([
        'goal_amount' => 1000,
        'donated_amount' => 250
    ]);
    
    $progress = ($campaign->donated_amount / $campaign->goal_amount) * 100;
    
    expect($progress)->toBe(25.0);
});

test('campaign progress calculation handles zero goal', function () {
    $campaign = new Campaign([
        'goal_amount' => 0,
        'donated_amount' => 100
    ]);
    
    $progress = $campaign->goal_amount > 0 ? ($campaign->donated_amount / $campaign->goal_amount) * 100 : 0;
    
    expect($progress)->toBe(0);
});

test('donation amount is cast to decimal', function () {
    $donation = new Donation();
    
    expect($donation->getCasts())->toHaveKey('amount');
    expect($donation->getCasts()['amount'])->toBe('decimal:2');
});

test('notification data is cast to array', function () {
    $notification = new Notification();
    
    expect($notification->getCasts())->toHaveKey('data');
    expect($notification->getCasts()['data'])->toBe('array');
});

test('notification read_at is cast to datetime', function () {
    $notification = new Notification();
    
    expect($notification->getCasts())->toHaveKey('read_at');
    expect($notification->getCasts()['read_at'])->toBe('datetime');
});
