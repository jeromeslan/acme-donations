<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CampaignController extends Controller
{
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $cacheKey = 'campaigns:index:' . md5(json_encode($request->query()) ?: '');
        $ttl = 60; // seconds
        $query = Campaign::query()->with('category')->withCount('successfulDonations as donations_count')
            ->when($request->filled('q'), function ($query) use ($request) {
                $value = (string)$request->query('q');
                return $query->where('title', 'like', "%{$value}%");
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $value = (string)$request->query('status');
                return $query->where('status', $value);
            }, function ($query) {
                // Default: only show active campaigns for public access
                return $query->where('status', 'active');
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
            $fallback = Campaign::query()->with('category')->withCount('successfulDonations as donations_count')->orderByDesc('id');
            $page = $fallback->paginate(perPage: (int)$request->integer('per_page', 12));
        }

        return response()->json($page);
    }

    public function featured(): \Illuminate\Http\JsonResponse
    {
        $featured = Cache::store('redis')->tags(['campaigns'])->remember('campaigns:featured', 60, fn() => Campaign::query()
            ->where('featured', true)->where('status', 'active')
            ->with('category')->withCount('successfulDonations as donations_count')
            ->orderByDesc('id')->limit(8)->get());
        
        return response()->json($featured);
    }

    public function show(Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        $cacheKey = "campaigns:show:{$campaign->id}";
        
        $campaignData = Cache::store('redis')
            ->tags(["campaign:{$campaign->id}", 'campaigns'])
            ->remember($cacheKey, 60, function() use ($campaign) {
                // Fresh fetch from database to ensure we have latest donated_amount
                $freshCampaign = Campaign::with('category')
                    ->withCount('successfulDonations as donations_count')
                    ->find($campaign->id);
                    
                return $freshCampaign;
            });
        
        return response()->json($campaignData);
    }

    public function store(Request $request): \Illuminate\Http\JsonResponse
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

    public function update(Request $request, Campaign $campaign): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Ensure only the creator can update their campaign
        if ($campaign->creator_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized to update this campaign.'], 403);
        }

        // Only allow updates if campaign is in draft or pending status
        if (!in_array($campaign->status, ['draft', 'pending'])) {
            return response()->json(['message' => 'Cannot update campaign with current status.'], 422);
        }

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

    public function destroy(Campaign $campaign): \Illuminate\Http\Response
    {
        $campaign->delete();
        Cache::store('redis')->tags(["campaign:{$campaign->id}", 'campaigns'])->flush();
        return response()->noContent();
    }

    public function myCampaigns(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $campaigns = Campaign::query()
            ->where('creator_id', $user->id)
            ->with('category')
            ->withCount('successfulDonations as donations_count')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'campaigns' => $campaigns
        ]);
    }
}


