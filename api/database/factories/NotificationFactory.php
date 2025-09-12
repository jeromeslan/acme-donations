<?php

namespace Database\Factories;

use App\Models\Notification;
use App\Models\User;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Notification>
 */
class NotificationFactory extends Factory
{
    protected $model = Notification::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'campaign_id' => Campaign::factory(),
            'type' => $this->faker->randomElement(['campaign_approved', 'campaign_rejected', 'donation_received']),
            'title' => $this->faker->words(3, true),
            'message' => $this->faker->text(200),
            'data' => [
                'campaign_title' => $this->faker->words(4, true),
                'campaign_id' => $this->faker->numberBetween(1, 100),
            ],
            'read_at' => $this->faker->optional(0.3)->dateTimeBetween('-1 month', 'now'),
        ];
    }

    public function unread(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => null,
        ]);
    }

    public function read(): static
    {
        return $this->state(fn (array $attributes) => [
            'read_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ]);
    }

    public function campaignApproved(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'campaign_approved',
            'title' => 'Campaign Approved! 🎉',
            'message' => 'Your campaign has been approved and is now live!',
        ]);
    }

    public function campaignRejected(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'campaign_rejected',
            'title' => 'Campaign Needs Updates',
            'message' => 'Your campaign needs some updates before it can go live.',
        ]);
    }
}
