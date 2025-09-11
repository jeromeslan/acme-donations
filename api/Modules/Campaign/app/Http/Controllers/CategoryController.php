<?php

namespace Modules\Campaign\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends \App\Http\Controllers\Controller
{
    public function index()
    {
        return Category::orderBy('name')->get();
    }

    public function show(Category $category)
    {
        return $category->load('campaigns');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories',
        ]);

        return Category::create($data);
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => 'sometimes|string|max:255',
            'slug' => 'sometimes|string|max:255|unique:categories,slug,' . $category->id,
        ]);

        $category->update($data);
        return $category;
    }

    public function destroy(Category $category)
    {
        // Vérifier si la catégorie a des campagnes
        if ($category->campaigns()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete category with existing campaigns'
            ], 400);
        }

        $category->delete();
        return response()->noContent();
    }
}
