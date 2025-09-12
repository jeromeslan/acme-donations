<?php

use App\Models\Notification;
use App\Models\User;
use App\Models\Campaign;

test('notification can be created with factory', function () {
    $notification = Notification::factory()->create();
    
    expect($notification)->toBeInstanceOf(Notification::class);
    expect($notification->type)->toBeString();
    expect($notification->title)->toBeString();
    expect($notification->message)->toBeString();
    expect($notification->data)->toBeArray();
});

test('notification belongs to user', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id]);
    
    expect($notification->user)->toBeInstanceOf(User::class);
    expect($notification->user->id)->toBe($user->id);
});

test('notification can belong to campaign', function () {
    $campaign = Campaign::factory()->create();
    $notification = Notification::factory()->create(['campaign_id' => $campaign->id]);
    
    expect($notification->campaign)->toBeInstanceOf(Campaign::class);
    expect($notification->campaign->id)->toBe($campaign->id);
});

test('notification can be unread', function () {
    $notification = Notification::factory()->unread()->create();
    
    expect($notification->read_at)->toBeNull();
});

test('notification can be read', function () {
    $notification = Notification::factory()->read()->create();
    
    expect($notification->read_at)->not->toBeNull();
    expect($notification->read_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('notification can be marked as read', function () {
    $notification = Notification::factory()->unread()->create();
    
    expect($notification->read_at)->toBeNull();
    
    $notification->markAsRead();
    
    expect($notification->read_at)->not->toBeNull();
    expect($notification->read_at)->toBeInstanceOf(\Carbon\Carbon::class);
});

test('notification markAsRead is idempotent', function () {
    $notification = Notification::factory()->read()->create();
    $originalReadAt = $notification->read_at;
    
    $notification->markAsRead();
    
    expect($notification->read_at)->toBe($originalReadAt);
});

test('notification can be campaign approved type', function () {
    $notification = Notification::factory()->campaignApproved()->create();
    
    expect($notification->type)->toBe('campaign_approved');
    expect($notification->title)->toBe('Campaign Approved! 🎉');
    expect($notification->message)->toContain('approved');
});

test('notification can be campaign rejected type', function () {
    $notification = Notification::factory()->campaignRejected()->create();
    
    expect($notification->type)->toBe('campaign_rejected');
    expect($notification->title)->toBe('Campaign Needs Updates');
    expect($notification->message)->toContain('updates');
});

test('notification has correct fillable attributes', function () {
    $notification = new Notification();
    
    expect($notification->getFillable())->toContain(
        'user_id', 'campaign_id', 'type', 'title', 'message', 'data', 'read_at'
    );
});

test('notification data is cast to array', function () {
    $notification = Notification::factory()->create([
        'data' => ['key' => 'value', 'number' => 123]
    ]);
    
    expect($notification->data)->toBeArray();
    expect($notification->data['key'])->toBe('value');
    expect($notification->data['number'])->toBe(123);
});
