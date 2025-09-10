<?php

namespace Database\Seeders;

use App\Models\{User, Category, Campaign, Donation, DonationReceipt};
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DemoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            // Roles & permissions
            $roles = ['admin','employee','creator'];
            foreach ($roles as $r) { Role::firstOrCreate(['name' => $r]); }
            // Users
            $admin = User::firstOrCreate(
                ['email' => 'admin@acme.test'],
                [
                    'name' => 'Admin',
                    'email_verified_at' => now(),
                    // Use plain value; 'hashed' cast on User will hash with current algorithm
                    'password' => 'password',
                ]
            );
            if (! $admin->hasRole('admin')) { $admin->assignRole('admin'); }
            $employees = User::factory()->count(5)->create();
            foreach ($employees as $e) { $e->assignRole('employee'); }
            $creators = User::factory()->count(5)->create();
            foreach ($creators as $c) { $c->assignRole('creator'); }

            // Categories
            $categories = collect([
                'Education','Health','Environment','Community','Arts','Emergency','Research'
            ])->map(fn($n) => Category::firstOrCreate(['slug' => Str::slug($n)], ['name' => $n]));

            // Campaigns
            $statuses = ['draft','pending','active','completed'];
            $campaigns = collect(range(1, 30))->map(function ($i) use ($categories, $creators, $statuses) {
                return Campaign::create([
                    'category_id' => $categories->random()->id,
                    'creator_id' => $creators->random()->id,
                    'title' => "Campaign #$i",
                    'description' => 'A sample CSR campaign',
                    'goal_amount' => random_int(1_000, 50_000),
                    'donated_amount' => 0,
                    'status' => collect($statuses)->random(),
                    'featured' => (bool)random_int(0,1),
                    'published_at' => now()->subDays(random_int(0, 90)),
                ]);
            });

            // Guarantee at least some featured & active campaigns for demo
            $campaigns->take(6)->each(function (Campaign $c) {
                $c->update([
                    'status' => 'active',
                    'featured' => true,
                    'published_at' => now()->subDays(random_int(0, 10)),
                ]);
            });

            // Donations & receipts
            $donors = User::factory()->count(50)->create();
            $allUsers = $donors->merge($employees);
            $donations = 0;
            foreach ($campaigns as $campaign) {
                $count = random_int(5, 30);
                for ($j = 0; $j < $count; $j++) {
                    $amount = random_int(5, 500);
                    $donation = Donation::create([
                        'campaign_id' => $campaign->id,
                        'user_id' => $allUsers->random()->id,
                        'amount' => $amount,
                        'status' => 'succeeded',
                        'payment_reference' => Str::uuid()->toString(),
                        'correlation_id' => Str::uuid()->toString(),
                    ]);
                    $campaign->increment('donated_amount', $amount);
                    DonationReceipt::create([
                        'donation_id' => $donation->id,
                        'receipt_number' => 'R-' . Str::upper(Str::random(10)),
                        'issued_at' => now()->subDays(random_int(0, 90)),
                        'metadata' => ['method' => 'mock'],
                    ]);
                    $donations++;
                }
            }

            $this->command?->info("Seeded campaigns: " . $campaigns->count() . ", donations: $donations");
        });
    }
}


