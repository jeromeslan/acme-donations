<?php

namespace App\Services;

use App\Contracts\PaymentGateway;

class MockPaymentGateway implements PaymentGateway
{
    public function processPayment(array $data): array
    {
        // Simulate payment processing delay
        sleep(1);

        // Simulate random success/failure (90% success rate)
        $success = rand(1, 10) <= 9;

        if ($success) {
            return [
                'transaction_id' => 'mock_' . uniqid(),
                'status' => 'completed',
                'amount' => $data['amount'],
                'currency' => 'EUR',
                'processed_at' => now(),
            ];
        } else {
            throw new \Exception('Payment failed: Insufficient funds');
        }
    }

    public function handleWebhook(array $data): array
    {
        // Mock webhook handling
        \Log::info('Mock webhook received', $data);

        return [
            'status' => 'processed',
            'transaction_id' => $data['transaction_id'] ?? 'unknown',
        ];
    }

    public function refund(string $transactionId, float $amount): array
    {
        // Simulate refund processing
        sleep(1);

        return [
            'transaction_id' => $transactionId,
            'refund_id' => 'refund_' . uniqid(),
            'status' => 'refunded',
            'amount' => $amount,
            'processed_at' => now(),
        ];
    }
}
