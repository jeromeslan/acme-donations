<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CampaignController extends Controller
{
    public function index(Request $request)
    {
        $cacheKey = 'campaigns:index:' . md5(json_encode($request->query()));
        $ttl = 60; // seconds
        $query = Campaign::query()->with('category')->withCount('donations')
            ->when($request->filled('q'), function ($query) use ($request) {
                $value = (string)$request->query('q');
                return $query->where('title', 'like', "%{$value}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $value = (string)$request->query('status');
                return $query->where('status', $value);
            })
            ->when($request->filled('category_id'), function ($query) use ($request) {
                $value = (int)$request->query('category_id');
                return $query->where('category_id', $value);
            })
            ->orderByDesc('id');

        // Primary fetch (with optional cache)
        $fetch = function () use ($request, $query) {
            return $query->paginate(perPage: (int)$request->integer('per_page', 12));
        };

        $page = $request->boolean('nocache')
            ? $fetch()
            : Cache::store('redis')->tags(['campaigns'])->remember($cacheKey, $ttl, $fetch);

        // Safety fallback: if empty page but campaigns exist, return latest campaigns
        if ($page->total() === 0 && \App\Models\Campaign::query()->count() > 0) {
            $fallback = Campaign::query()->with('category')->withCount('donations')->orderByDesc('id');
            return $fallback->paginate(perPage: (int)$request->integer('per_page', 12));
        }

        return $page;
    }

    public function featured()
    {
        return Cache::store('redis')->tags(['campaigns'])->remember('campaigns:featured', 60, fn() => Campaign::query()
            ->where('featured', true)->where('status', 'active')
            ->with('category')->withCount('donations')
            ->orderByDesc('id')->limit(8)->get());
    }

    public function show(Campaign $campaign)
    {
        return Cache::store('redis')->tags(["campaign:{$campaign->id}", 'campaigns'])->remember("campaigns:show:{$campaign->id}", 60, fn() => $campaign->load('category'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'goal_amount' => 'required|numeric|min:1',
            'category_id' => 'required|exists:categories,id',
            'featured' => 'boolean',
        ]);
        $campaign = Campaign::create($data + [
            'creator_id' => (int)$request->user()?->id,
            'status' => 'pending',
            'donated_amount' => 0,
        ]);
        Cache::store('redis')->tags(['campaigns'])->flush();
        return response()->json($campaign, 201);
    }

    public function update(Request $request, Campaign $campaign)
    {
        $data = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'goal_amount' => 'sometimes|numeric|min:1',
            'category_id' => 'sometimes|exists:categories,id',
            'featured' => 'boolean',
            'status' => 'sometimes|in:draft,pending,active,completed,archived',
        ]);
        $campaign->update($data);
        Cache::store('redis')->tags(["campaign:{$campaign->id}", 'campaigns'])->flush();
        return response()->json($campaign);
    }

    public function destroy(Campaign $campaign)
    {
        $campaign->delete();
        Cache::store('redis')->tags(["campaign:{$campaign->id}", 'campaigns'])->flush();
        return response()->noContent();
    }
}


