<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;

uses(RefreshDatabase::class);

test('user can login with valid credentials', function () {
    // Create admin role
    Role::create(['name' => 'admin']);
    
    // Create user
    $user = User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);
    $user->assignRole('admin');

    // Attempt login
    $response = $this->post('/api/login', [
        'email' => 'test@example.com',
        'password' => 'password',
    ]);

    $response->assertStatus(200);
    $this->assertAuthenticated();
});

test('user cannot login with invalid credentials', function () {
    $response = $this->post('/api/login', [
        'email' => 'wrong@example.com',
        'password' => 'wrongpassword',
    ]);

    $response->assertStatus(401);
    $this->assertGuest();
});

test('authenticated user can access profile', function () {
    Role::create(['name' => 'admin']);
    
    $user = User::factory()->create();
    $user->assignRole('admin');

    $response = $this->actingAs($user)->get('/api/me');
    
    $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'name', 
                'email',
                'roles'
            ]);
});

test('unauthenticated user cannot access protected routes', function () {
    $response = $this->get('/api/me');
    $response->assertStatus(401);
});
