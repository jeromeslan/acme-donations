<?php

namespace App\Jobs;

use App\Models\Donation;
use App\Models\Campaign;
use App\Contracts\PaymentGateway;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class ProcessDonationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    /** @var array<int> */
    public array $backoff = [10, 30, 60];

    protected int $donationId;

    public function __construct(int $donationId)
    {
        $this->donationId = $donationId;
    }

    public function handle(PaymentGateway $paymentGateway): void
    {
        $donation = Donation::findOrFail($this->donationId);
        
        try {
            Log::info('Processing donation', ['donation_id' => $donation->id]);

            // Process payment
            $paymentResult = $paymentGateway->processPayment([
                'donation_id' => $donation->id,
                'amount' => $donation->amount,
                'payment_method' => 'mock',
            ]);

            // Update donation status
            $donation->update([
                'status' => 'completed',
                'transaction_id' => $paymentResult['transaction_id'],
                'processed_at' => now(),
            ]);

            // Update campaign donated amount
            $campaign = $donation->campaign;
            if (!$campaign) {
                throw new \Exception('Campaign not found for donation');
            }
            
            $oldAmount = $campaign->donated_amount;
            $campaign->increment('donated_amount', (float)$donation->amount);
            
            // Refresh the campaign instance to get updated values
            $campaign->refresh();
            $newAmount = $campaign->donated_amount;
            
            Log::info('Campaign amount updated', [
                'campaign_id' => $campaign->id,
                'old_amount' => $oldAmount,
                'donation_amount' => $donation->amount,
                'new_amount' => $newAmount
            ]);

            // Clear cache
            Cache::store('redis')->tags(['campaigns', "campaign:{$campaign->id}"])->flush();

            // Send notification to donor (if user exists)
            $donor = $donation->user;
            if ($donor) {
                // Note: DonationProcessed notification class needs to be created
                // $donor->notify(new \App\Notifications\DonationProcessed($donation));
            }

            // Send notification to campaign creator if donation is significant
            if ($donation->amount >= 50) {
                $creator = $campaign->creator;
                if ($creator) {
                    // Note: NewDonation notification class needs to be created
                    // $creator->notify(new \App\Notifications\NewDonation($donation));
                }
            }

            Log::info('Donation processed successfully', ['donation_id' => $donation->id]);

        } catch (\Exception $e) {
            Log::error('Failed to process donation', [
                'donation_id' => $donation->id,
                'error' => $e->getMessage()
            ]);

            $donation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Donation processing failed permanently', [
            'donation_id' => $this->donationId,
            'error' => $exception->getMessage()
        ]);

        // Notify admin of failed donation
        // You could dispatch another job or send notification here
    }
}