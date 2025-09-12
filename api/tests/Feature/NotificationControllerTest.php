<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Notification;
use App\Models\Category;
use Spatie\Permission\Models\Role;

test('authenticated user can get their notifications', function () {
    Role::create(['name' => 'creator']);
    $user = User::factory()->create();
    $user->assignRole('creator');
    
    Category::factory()->create();
    $campaign = Campaign::factory()->create(['creator_id' => $user->id]);
    
    Notification::factory()->unread()->count(3)->create(['user_id' => $user->id]);
    Notification::factory()->read()->count(2)->create(['user_id' => $user->id]);
    Notification::factory()->create(); // Different user's notification
    
    $response = $this->actingAs($user)->get('/api/me/notifications');
    
    $response->assertStatus(200);
    $response->assertJsonStructure([
        'notifications' => [
            '*' => [
                'id', 'type', 'title', 'message', 'data', 
                'read_at', 'created_at', 'campaign'
            ]
        ],
        'unread_count'
    ]);
    
    $data = $response->json();
    expect($data['notifications'])->toHaveCount(5); // Only user's notifications
    expect($data['unread_count'])->toBe(3);
});

test('user can mark notification as read', function () {
    Role::create(['name' => 'creator']);
    $user = User::factory()->create();
    $user->assignRole('creator');
    
    $notification = Notification::factory()->unread()->create(['user_id' => $user->id]);
    
    expect($notification->read_at)->toBeNull();
    
    $response = $this->actingAs($user)->post("/api/notifications/{$notification->id}/read");
    
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'Notification marked as read'
    ]);
    
    $notification->refresh();
    expect($notification->read_at)->not->toBeNull();
});

test('user can mark all notifications as read', function () {
    Role::create(['name' => 'creator']);
    $user = User::factory()->create();
    $user->assignRole('creator');
    
    Notification::factory()->unread()->count(3)->create(['user_id' => $user->id]);
    Notification::factory()->read()->count(2)->create(['user_id' => $user->id]);
    
    $response = $this->actingAs($user)->post('/api/notifications/read-all');
    
    $response->assertStatus(200);
    $response->assertJson([
        'message' => 'All notifications marked as read'
    ]);
    
    $unreadCount = Notification::where('user_id', $user->id)
        ->whereNull('read_at')
        ->count();
    
    expect($unreadCount)->toBe(0);
});

test('user cannot mark other user notification as read', function () {
    Role::create(['name' => 'creator']);
    $user1 = User::factory()->create();
    $user1->assignRole('creator');
    
    $user2 = User::factory()->create();
    $user2->assignRole('creator');
    
    $notification = Notification::factory()->unread()->create(['user_id' => $user2->id]);
    
    $response = $this->actingAs($user1)->post("/api/notifications/{$notification->id}/read");
    
    $response->assertStatus(404); // Not found because it doesn't belong to user1
});

test('unauthenticated user cannot access notifications', function () {
    $response = $this->get('/api/me/notifications');
    
    $response->assertStatus(401);
});

test('notification includes campaign information when available', function () {
    Role::create(['name' => 'creator']);
    $user = User::factory()->create();
    $user->assignRole('creator');
    
    Category::factory()->create();
    $campaign = Campaign::factory()->create(['creator_id' => $user->id]);
    
    $notification = Notification::factory()->create([
        'user_id' => $user->id,
        'campaign_id' => $campaign->id,
        'type' => 'campaign_approved'
    ]);
    
    $response = $this->actingAs($user)->get('/api/me/notifications');
    
    $response->assertStatus(200);
    $notifications = $response->json('notifications');
    
    expect($notifications[0]['campaign'])->not->toBeNull();
    expect($notifications[0]['campaign']['id'])->toBe($campaign->id);
    expect($notifications[0]['campaign']['title'])->toBe($campaign->title);
});

test('notification data is properly formatted', function () {
    Role::create(['name' => 'creator']);
    $user = User::factory()->create();
    $user->assignRole('creator');
    
    $notification = Notification::factory()->campaignApproved()->create([
        'user_id' => $user->id,
        'data' => [
            'campaign_title' => 'Test Campaign',
            'campaign_id' => 123,
            'featured' => true
        ]
    ]);
    
    $response = $this->actingAs($user)->get('/api/me/notifications');
    
    $response->assertStatus(200);
    $notifications = $response->json('notifications');
    
    expect($notifications[0]['data'])->toBeArray();
    expect($notifications[0]['data']['campaign_title'])->toBe('Test Campaign');
    expect($notifications[0]['data']['campaign_id'])->toBe(123);
    expect($notifications[0]['data']['featured'])->toBeTrue();
});
