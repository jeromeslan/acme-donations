<?php

use App\Models\Category;
use App\Models\Campaign;

test('category can be created with factory', function () {
    $category = Category::factory()->create();
    
    expect($category)->toBeInstanceOf(Category::class);
    expect($category->name)->toBeString();
    expect($category->description)->toBeString();
});

test('category has campaigns relationship', function () {
    $category = Category::factory()->create();
    $campaign = Campaign::factory()->create(['category_id' => $category->id]);
    
    expect($category->campaigns)->toHaveCount(1);
    expect($category->campaigns->first())->toBeInstanceOf(Campaign::class);
    expect($category->campaigns->first()->category_id)->toBe($category->id);
});

test('category has correct fillable attributes', function () {
    $category = new Category();
    
    expect($category->getFillable())->toContain('name', 'slug');
});

test('category name is from predefined list', function () {
    $validNames = [
        'Education', 'Healthcare', 'Environment', 'Animal Welfare',
        'Disaster Relief', 'Community Development', 'Arts & Culture',
        'Technology', 'Sports', 'Religious'
    ];
    
    $category = Category::factory()->create();
    
    expect($validNames)->toContain($category->name);
});
