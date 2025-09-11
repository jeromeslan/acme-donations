<?php

namespace Modules\Payment\Http\Controllers;

use App\Contracts\PaymentGateway;
use Illuminate\Http\Request;

class PaymentController extends \App\Http\Controllers\Controller
{
    protected PaymentGateway $paymentGateway;

    public function __construct(PaymentGateway $paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function process(Request $request)
    {
        $data = $request->validate([
            'donation_id' => 'required|exists:donations,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
        ]);

        try {
            $result = $this->paymentGateway->processPayment($data);

            return response()->json([
                'success' => true,
                'transaction_id' => $result['transaction_id'],
                'status' => $result['status'],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function webhook(Request $request)
    {
        try {
            $result = $this->paymentGateway->handleWebhook($request->all());
            return response()->json(['status' => 'processed']);
        } catch (\Exception $e) {
            \Log::error('Payment webhook error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 400);
        }
    }
}
