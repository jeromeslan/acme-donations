<?php

use App\Models\User;
use App\Models\Campaign;
use App\Models\Notification;
use App\Models\Donation;
use Spatie\Permission\Models\Role;

test('user can be created with factory', function () {
    $user = User::factory()->create();
    
    expect($user)->toBeInstanceOf(User::class);
    expect($user->name)->toBeString();
    expect($user->email)->toBeString();
    expect($user->email)->toContain('@');
});

test('user has campaigns relationship', function () {
    $user = User::factory()->create();
    $campaign = Campaign::factory()->create(['creator_id' => $user->id]);
    
    expect($user->campaigns)->toHaveCount(1);
    expect($user->campaigns->first())->toBeInstanceOf(Campaign::class);
    expect($user->campaigns->first()->creator_id)->toBe($user->id);
});

test('user has notifications relationship', function () {
    $user = User::factory()->create();
    $notification = Notification::factory()->create(['user_id' => $user->id]);
    
    expect($user->notifications)->toHaveCount(1);
    expect($user->notifications->first())->toBeInstanceOf(Notification::class);
    expect($user->notifications->first()->user_id)->toBe($user->id);
});

test('user can have donations relationship', function () {
    $user = User::factory()->create();
    $donation = Donation::factory()->create(['user_id' => $user->id]);
    
    expect($user->donations)->toHaveCount(1);
    expect($user->donations->first())->toBeInstanceOf(Donation::class);
    expect($user->donations->first()->user_id)->toBe($user->id);
});

test('user can be assigned roles', function () {
    Role::create(['name' => 'admin']);
    $user = User::factory()->create();
    
    $user->assignRole('admin');
    
    expect($user->hasRole('admin'))->toBeTrue();
    expect($user->roles)->toHaveCount(1);
});

test('user password is hashed when created', function () {
    $user = User::factory()->create(['password' => 'plaintext']);
    
    expect($user->password)->not->toBe('plaintext');
    expect($user->password)->toHaveLength(60); // bcrypt hash length
});
