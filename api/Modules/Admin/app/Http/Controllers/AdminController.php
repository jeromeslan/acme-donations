<?php

namespace Modules\Admin\Http\Controllers;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AdminController extends \App\Http\Controllers\Controller
{
    public function dashboard()
    {
        return [
            'stats' => [
                'total_campaigns' => Campaign::count(),
                'active_campaigns' => Campaign::where('status', 'active')->count(),
                'pending_campaigns' => Campaign::where('status', 'pending')->count(),
                'total_donations' => Donation::count(),
                'total_raised' => Donation::where('status', 'completed')->sum('amount'),
                'total_users' => User::count(),
            ],
            'recent_campaigns' => Campaign::with('creator')->orderByDesc('created_at')->limit(10)->get(),
            'recent_donations' => Donation::with(['donor', 'campaign'])->orderByDesc('created_at')->limit(10)->get(),
        ];
    }

    public function campaigns(Request $request)
    {
        return Campaign::with(['creator', 'category'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function approveCampaign(Request $request, Campaign $campaign)
    {
        $campaign->update(['status' => 'active']);
        Cache::store('redis')->tags(['campaigns'])->flush();
        return response()->json(['message' => 'Campaign approved']);
    }

    public function rejectCampaign(Request $request, Campaign $campaign)
    {
        $campaign->update(['status' => 'rejected']);
        Cache::store('redis')->tags(['campaigns'])->flush();
        return response()->json(['message' => 'Campaign rejected']);
    }

    public function users()
    {
        return User::with('roles')->orderByDesc('created_at')->paginate(20);
    }

    public function health()
    {
        return [
            'database' => $this->checkDatabase(),
            'redis' => $this->checkRedis(),
            'queue' => $this->checkQueue(),
        ];
    }

    private function checkDatabase()
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection OK'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Database connection failed: ' . $e->getMessage()];
        }
    }

    private function checkRedis()
    {
        try {
            Cache::store('redis')->put('health_check', 'ok', 1);
            return ['status' => 'healthy', 'message' => 'Redis connection OK'];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Redis connection failed: ' . $e->getMessage()];
        }
    }

    private function checkQueue()
    {
        try {
            $pendingJobs = \DB::table('jobs')->count();
            return [
                'status' => 'healthy',
                'message' => 'Queue OK',
                'pending_jobs' => $pendingJobs
            ];
        } catch (\Exception $e) {
            return ['status' => 'unhealthy', 'message' => 'Queue check failed: ' . $e->getMessage()];
        }
    }
}
