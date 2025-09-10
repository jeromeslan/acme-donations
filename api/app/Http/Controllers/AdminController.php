<?php

namespace App\Http\Controllers;

use App\Models\{Donation, Campaign};
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function kpis()
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
}


