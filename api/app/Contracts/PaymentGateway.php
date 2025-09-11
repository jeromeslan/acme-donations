<?php

namespace App\Contracts;

interface PaymentGateway
{
    /**
     * Process a payment
     *
     * @param array $data
     * @return array
     */
    public function processPayment(array $data): array;

    /**
     * Handle webhook from payment provider
     *
     * @param array $data
     * @return array
     */
    public function handleWebhook(array $data): array;

    /**
     * Refund a payment
     *
     * @param string $transactionId
     * @param float $amount
     * @return array
     */
    public function refund(string $transactionId, float $amount): array;
}
