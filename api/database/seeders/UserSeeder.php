<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Créer les rôles s'ils n'existent pas
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $userRole = Role::firstOrCreate(['name' => 'user']);
        $creatorRole = Role::firstOrCreate(['name' => 'creator']);

        // Créer l'utilisateur admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@acme.test'],
            [
                'name' => 'Admin User',
                'email' => 'admin@acme.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole($adminRole);

        // Créer l'utilisateur normal
        $user = User::updateOrCreate(
            ['email' => 'user@acme.test'],
            [
                'name' => 'Regular User',
                'email' => 'user@acme.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $user->assignRole($userRole);

        // Créer un créateur de campagnes
        $creator = User::updateOrCreate(
            ['email' => 'creator@acme.test'],
            [
                'name' => 'Campaign Creator',
                'email' => 'creator@acme.test',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $creator->assignRole($creatorRole);

        $this->command->info('Users created successfully!');
        $this->command->info('Admin: admin@acme.test / password');
        $this->command->info('User: user@acme.test / password');
        $this->command->info('Creator: creator@acme.test / password');
    }
}
