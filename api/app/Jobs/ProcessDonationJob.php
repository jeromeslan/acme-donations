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

    public $tries = 3;
    public $backoff = [10, 30, 60];

    protected Donation $donation;

    public function __construct(Donation $donation)
    {
        $this->donation = $donation;
    }

    public function handle(PaymentGateway $paymentGateway): void
    {
        try {
            Log::info('Processing donation', ['donation_id' => $this->donation->id]);

            // Process payment
            $paymentResult = $paymentGateway->processPayment([
                'donation_id' => $this->donation->id,
                'amount' => $this->donation->amount,
                'payment_method' => 'mock',
            ]);

            // Update donation status
            $this->donation->update([
                'status' => 'completed',
                'transaction_id' => $paymentResult['transaction_id'],
                'processed_at' => now(),
            ]);

            // Update campaign donated amount
            $campaign = $this->donation->campaign;
            $campaign->increment('donated_amount', $this->donation->amount);

            // Clear cache
            Cache::store('redis')->tags(['campaigns', "campaign:{$campaign->id}"])->flush();

            // Send notification to donor
            $this->donation->donor->notify(new \App\Notifications\DonationProcessed($this->donation));

            // Send notification to campaign creator if donation is significant
            if ($this->donation->amount >= 50) {
                $campaign->creator->notify(new \App\Notifications\NewDonation($this->donation));
            }

            Log::info('Donation processed successfully', ['donation_id' => $this->donation->id]);

        } catch (\Exception $e) {
            Log::error('Failed to process donation', [
                'donation_id' => $this->donation->id,
                'error' => $e->getMessage()
            ]);

            $this->donation->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e; // Re-throw to trigger retry
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Donation processing failed permanently', [
            'donation_id' => $this->donation->id,
            'error' => $exception->getMessage()
        ]);

        // Notify admin of failed donation
        // You could dispatch another job or send notification here
    }
}