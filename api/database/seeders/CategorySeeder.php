<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Education', 'slug' => 'education'],
            ['name' => 'Santé', 'slug' => 'sante'],
            ['name' => 'Environnement', 'slug' => 'environnement'],
            ['name' => 'Culture', 'slug' => 'culture'],
            ['name' => 'Social', 'slug' => 'social'],
            ['name' => 'Sport', 'slug' => 'sport'],
            ['name' => 'Technologie', 'slug' => 'technologie'],
            ['name' => 'Arts', 'slug' => 'arts'],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
