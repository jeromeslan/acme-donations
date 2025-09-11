<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can list all modules', function () {
    $this->artisan('module:list')
        ->assertExitCode(0);
});

test('it has all required modules enabled', function () {
    $modules = ['Campaign', 'Donation', 'Admin', 'Payment', 'Notification'];
    
    foreach ($modules as $module) {
        $this->artisan('module:list')
            ->expectsOutput($module)
            ->assertExitCode(0);
    }
});

test('campaign module routes are loaded', function () {
    $response = $this->get('/api/v1/campaigns');
    $response->assertStatus(401); // Should be unauthorized, not 404
});

test('donation module routes are loaded', function () {
    $response = $this->get('/api/v1/donations');
    $response->assertStatus(401); // Should be unauthorized, not 404
});

test('admin module routes are loaded', function () {
    $response = $this->get('/api/v1/admins');
    $response->assertStatus(401); // Should be unauthorized, not 404
});