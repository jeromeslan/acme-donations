<?php

namespace App\Http\Controllers;

use App\Models\{Donation, Campaign, User, Notification};
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function kpis(): \Illuminate\Http\JsonResponse
    {
        $totals = Donation::selectRaw('COUNT(*) as donations_count, SUM(amount) as donations_sum')->first();
        $uniqueDonors = Donation::distinct('user_id')->count('user_id');
        $topCampaigns = Campaign::query()
            ->select('id','title','donated_amount')
            ->orderByDesc('donated_amount')
            ->limit(5)
            ->get();

        return response()->json([
            'donations' => [
                'count' => (int)($totals->donations_count ?? 0),
                'sum' => (float)($totals->donations_sum ?? 0),
                'unique_donors' => (int)$uniqueDonors,
            ],
            'top_campaigns' => $topCampaigns,
        ]);
    }

    public function publicStats(): \Illuminate\Http\JsonResponse
    {
        // Public statistics for the home page
        $totalRaised = Donation::sum('amount') ?: 0;
        $activeCampaigns = Campaign::where('status', 'active')->count();
        $totalDonations = Donation::count();
        
        return response()->json([
            'totalRaised' => (float)$totalRaised,
            'totalCampaigns' => (int)$activeCampaigns, // Frontend expects totalCampaigns
            'totalDonations' => (int)$totalDonations,
        ]);
    }

    public function dashboard(): \Illuminate\Http\JsonResponse
    {
        // Get campaign statistics - use existing status values
        $totalCampaigns = Campaign::count();
        $publishedCampaigns = Campaign::where('status', 'active')->count(); // Use 'active' instead of 'published'
        $pendingCampaigns = Campaign::where('status', 'pending')->count();
        $draftCampaigns = Campaign::where('status', 'draft')->count();
        
        // Get donation statistics
        $totalDonations = Donation::count();
        $totalRaised = Donation::sum('amount') ?: 0;
        
        // Get user statistics
        $totalUsers = User::count();
        
        // Recent activity (last 10 campaigns)
        $recentActivity = Campaign::query()
            ->select('id', 'title', 'status', 'created_at')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get()
            ->map(function ($campaign) {
                return [
                    'id' => $campaign->id,
                    'description' => "Campaign '{$campaign->title}' was " . $this->getStatusAction($campaign->status),
                    'created_at' => $campaign->created_at
                ];
            });

        return response()->json([
            'stats' => [
                'totalCampaigns' => $totalCampaigns,
                'publishedCampaigns' => $publishedCampaigns,
                'pendingCampaigns' => $pendingCampaigns,
                'draftCampaigns' => $draftCampaigns,
                'totalDonations' => $totalDonations,
                'totalRaised' => $totalRaised,
                'totalUsers' => $totalUsers,
            ],
            'recentActivity' => $recentActivity
        ]);
    }



    public function pendingCampaigns(): \Illuminate\Http\JsonResponse
    {
        $campaigns = Campaign::with(['category', 'creator'])
            ->where('status', 'pending')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'campaigns' => $campaigns
        ]);
    }

    private function getStatusAction(string $status): string
    {
        return match($status) {
            'active' => 'published',
            'pending' => 'submitted for review',
            'draft' => 'saved as draft',
            'archived' => 'rejected',
            default => 'created'
        };
    }

    public function approveCampaign(int $id): \Illuminate\Http\JsonResponse
    {
        /** @var Campaign $campaign */
        $campaign = Campaign::findOrFail($id);
        
        // Get featured status from request
        $featured = request('featured', false);
        
        $campaign->update([
            'status' => 'active',
            'featured' => $featured
        ]);

        // Create notification for campaign creator
        $featuredMessage = $featured ? ' and has been featured on the homepage!' : '!';
        Notification::create([
            'user_id' => $campaign->creator_id,
            'campaign_id' => $campaign->id,
            'type' => 'campaign_approved',
            'title' => 'Campaign Approved! 🎉',
            'message' => "Your campaign '{$campaign->title}' has been approved and is now live{$featuredMessage} Users can start donating to support your cause.",
            'data' => [
                'campaign_title' => $campaign->title,
                'campaign_id' => $campaign->id,
                'featured' => $featured
            ]
        ]);

        return response()->json([
            'message' => 'Campaign approved successfully',
            'campaign' => $campaign
        ]);
    }

    public function rejectCampaign(int $id): \Illuminate\Http\JsonResponse
    {
        /** @var Campaign $campaign */
        $campaign = Campaign::findOrFail($id);
        $reason = request('reason', 'No reason provided');
        
        $campaign->update([
            'status' => 'archived',
            'rejection_reason' => $reason
        ]);

        // Create notification for campaign creator
        Notification::create([
            'user_id' => $campaign->creator_id,
            'campaign_id' => $campaign->id,
            'type' => 'campaign_rejected',
            'title' => 'Campaign Needs Updates',
            'message' => "Your campaign '{$campaign->title}' needs some updates before it can go live. Please review the feedback and resubmit.",
            'data' => [
                'campaign_title' => $campaign->title,
                'campaign_id' => $campaign->id,
                'rejection_reason' => $reason
            ]
        ]);

        return response()->json([
            'message' => 'Campaign rejected successfully',
            'campaign' => $campaign
        ]);
    }

    public function allCampaigns(): \Illuminate\Http\JsonResponse
    {
        $campaigns = Campaign::query()
            ->with(['category', 'creator:id,name,email'])
            ->withCount('successfulDonations as donations_count')
            ->orderByDesc('created_at')
            ->get();

        return response()->json([
            'campaigns' => $campaigns
        ]);
    }
}


