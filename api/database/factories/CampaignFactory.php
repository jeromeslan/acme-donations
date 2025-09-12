<?php

namespace Database\Factories;

use App\Models\Campaign;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Campaign>
 */
class CampaignFactory extends Factory
{
    protected $model = Campaign::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->words(4, true),
            'description' => $this->faker->text(500),
            'goal_amount' => $this->faker->numberBetween(1000, 100000),
            'donated_amount' => $this->faker->numberBetween(0, 50000),
            'status' => $this->faker->randomElement(['draft', 'pending', 'active', 'completed', 'archived']),
            'featured' => $this->faker->boolean(20), // 20% chance of being featured
            'creator_id' => User::factory(),
            'category_id' => Category::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'pending',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'active',
        ]);
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
            'donated_amount' => $this->faker->numberBetween(1000, 100000),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'featured' => true,
            'status' => 'active',
        ]);
    }
}
