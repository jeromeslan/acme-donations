<?php

namespace App\Http\Controllers;

use App\Models\{Campaign, Donation, DonationReceipt};
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Cache;

class DonationController extends Controller
{
    public function store(Request $request, Campaign $campaign)
    {
        $data = $request->validate(['amount' => 'required|numeric|min:1']);
        $correlationId = (string) Str::uuid();
        $donation = Donation::create([
            'campaign_id' => $campaign->id,
            'user_id' => $request->user()?->id,
            'amount' => $data['amount'],
            'status' => 'processing',
            'correlation_id' => $correlationId,
        ]);
        $job = new \App\Jobs\ProcessDonationJob($donation->id);
        Bus::dispatch($job->onQueue('donations'));
        return response()->json(['id' => $donation->id, 'correlation_id' => $correlationId], 202);
    }

    public function myDonations(Request $request)
    {
        $user = $request->user();
        $query = Donation::query()->with('campaign')->where('user_id', $user?->id)->orderByDesc('id');
        return $query->paginate(perPage: (int)$request->integer('per_page', 10));
    }

    public function receipt(Donation $donation)
    {
        $receipt = DonationReceipt::where('donation_id', $donation->id)->firstOrFail();
        return response()->json($receipt);
    }
}


