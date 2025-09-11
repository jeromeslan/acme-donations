<?php

namespace Modules\Donation\Http\Controllers;

use App\Models\Donation;
use App\Models\DonationReceipt;
use Illuminate\Http\Request;

class DonationController extends \App\Http\Controllers\Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'campaign_id' => 'required|exists:campaigns,id',
            'amount' => 'required|numeric|min:0.01',
            'message' => 'nullable|string|max:500',
            'anonymous' => 'boolean',
        ]);

        $donation = Donation::create($data + [
            'donor_id' => $request->user()?->id,
            'status' => 'pending',
        ]);

        // Process donation asynchronously via job
        \App\Jobs\ProcessDonationJob::dispatch($donation);

        return response()->json($donation, 201);
    }

    public function myDonations(Request $request)
    {
        return Donation::where('donor_id', $request->user()->id)
            ->with(['campaign'])
            ->orderByDesc('created_at')
            ->paginate(20);
    }

    public function receipt(Request $request, Donation $donation)
    {
        // Ensure user owns this donation
        if ($donation->donor_id !== $request->user()->id) {
            abort(403);
        }

        $receipt = DonationReceipt::firstOrCreate(
            ['donation_id' => $donation->id],
            [
                'receipt_number' => 'REC-' . date('Y') . '-' . str_pad($donation->id, 6, '0', STR_PAD_LEFT),
                'issued_at' => now(),
            ]
        );

        return response()->json($receipt);
    }
}
