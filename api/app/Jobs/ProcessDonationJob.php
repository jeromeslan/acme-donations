<?php

namespace App\Jobs;

use App\Models\{Donation, DonationReceipt, Campaign};
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class ProcessDonationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public int $donationId) {}

    public $queue = 'donations';

    public function handle(): void
    {
        $donation = Donation::findOrFail($this->donationId);
        // Mock payment success
        $donation->update([
            'status' => 'succeeded',
            'payment_reference' => Str::uuid()->toString(),
        ]);
        DonationReceipt::create([
            'donation_id' => $donation->id,
            'receipt_number' => 'R-' . Str::upper(Str::random(10)),
            'issued_at' => now(),
            'metadata' => ['gateway' => 'mock'],
        ]);
        Campaign::where('id', $donation->campaign_id)->increment('donated_amount', $donation->amount);
        // Invalidate caches
        Cache::forget("campaigns:show:{$donation->campaign_id}");
        Cache::delete('campaigns:index:*');
        Cache::delete('campaigns:featured');
    }
}


