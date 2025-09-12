<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->randomElement([
                'Education',
                'Healthcare',
                'Environment',
                'Animal Welfare',
                'Disaster Relief',
                'Community Development',
                'Arts & Culture',
                'Technology',
                'Sports',
                'Religious'
            ]),
            'slug' => $this->faker->slug(),
        ];
    }
}
