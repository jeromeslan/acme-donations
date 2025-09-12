<?php

namespace Database\Factories;

use App\Models\Donation;
use App\Models\User;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Donation>
 */
class DonationFactory extends Factory
{
    protected $model = Donation::class;

    public function definition(): array
    {
        return [
            'amount' => $this->faker->numberBetween(10, 1000),
            'user_id' => User::factory(),
            'campaign_id' => Campaign::factory(),
            'status' => $this->faker->randomElement(['pending', 'completed', 'failed']),
            'payment_reference' => $this->faker->uuid(),
            'correlation_id' => $this->faker->uuid(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function failed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'failed',
        ]);
    }
}
