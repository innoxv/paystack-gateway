<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yabacon\Paystack;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;

class PaymentController extends Controller
{
    public function processPayment(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:100', // in kobo (100 = 1 KES)
            'payment_method' => 'required|in:card,mpesa,airtel',
            'phone' => 'required_if:payment_method,mpesa,airtel',
            'reference' => 'required|string',
            'currency' => 'required|string|in:KES',
            'card' => 'required_if:payment_method,card|array',
            'card.number' => 'required_if:payment_method,card|string',
            'card.cvv' => 'required_if:payment_method,card|string',
            'card.expiry_month' => 'required_if:payment_method,card|string',
            'card.expiry_year' => 'required_if:payment_method,card|string',
        ]);

        $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));

        try {
            $paymentData = [
                'email' => 'customer@example.com', // Default email or get from request if needed
                'amount' => $request->input('amount'),
                'reference' => $request->input('reference'),
                'currency' => $request->input('currency'),
                'metadata' => [
                    'payment_method' => $request->input('payment_method'),
                    'custom_fields' => [
                        [
                            'display_name' => "Payment Method",
                            'variable_name' => "payment_method",
                            'value' => $request->input('payment_method')
                        ]
                    ]
                ]
            ];

            // Save payment initiation to DB
            Payment::create([
                'reference' => $request->input('reference'),
                'amount' => $request->input('amount'),
                'currency' => $request->input('currency'),
                'payment_method' => $request->input('payment_method'),
                'phone' => $request->input('phone'),
                'status' => 'pending',
            ]);

            // Handle different payment methods
            switch ($request->input('payment_method')) {
                case 'card':
                    // Process card payment
                    $paymentData['card'] = $request->input('card');
                    $response = $paystack->transaction->charge($paymentData);
                    break;

                case 'mpesa':
                    // Process M-Pesa payment
                    $paymentData['mobile_money'] = [
                        'phone' => $request->input('phone'),
                        'provider' => 'mpesa'
                    ];
                    $response = $paystack->transaction->initialize($paymentData);
                    break;

                case 'airtel':
                    // Process Airtel Money payment
                    $paymentData['mobile_money'] = [
                        'phone' => $request->input('phone'),
                        'provider' => 'airtel'
                    ];
                    $response = $paystack->transaction->initialize($paymentData);
                    break;
            }

            // Verify the payment was initiated successfully
            if (!$response->status) {
                throw new \Exception($response->message ?? 'Payment initiation failed');
            }

            return response()->json([
                'status' => true,
                'message' => 'Payment initiated successfully',
                'reference' => $request->input('reference'),
                'payment_method' => $request->input('payment_method')
            ]);

        } catch (\Exception $e) {
            Log::error('Paystack Error: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function checkPaymentStatus(Request $request)
    {
        $request->validate([
            'reference' => 'required|string'
        ]);

        // Check DB first
        $payment = Payment::where('reference', $request->input('reference'))->first();
        if ($payment) {
            if ($payment->status === 'success') {
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment verified successfully'
                ]);
            } elseif ($payment->status === 'failed') {
                return response()->json([
                    'status' => 'failed',
                    'message' => 'Payment failed'
                ]);
            }
            // else, still pending, fall through to Paystack API check
        }

        $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));
        try {
            $response = $paystack->transaction->verify([
                'reference' => $request->input('reference')
            ]);
            if ($response->data->status === 'success') {
                if ($payment) {
                    $payment->status = 'success';
                    $payment->save();
                }
                return response()->json([
                    'status' => 'success',
                    'message' => 'Payment verified successfully'
                ]);
            } else {
                return response()->json([
                    'status' => 'pending',
                    'message' => 'Payment is still pending'
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Paystack Verification Error: ' . $e->getMessage());
            return response()->json([
                'status' => 'failed',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function handleWebhook(Request $request)
{
    // Verify Paystack signature
    $signature = $request->header('x-paystack-signature');
    $computedSignature = hash_hmac('sha512', $request->getContent(), env('PAYSTACK_SECRET_KEY'));

    if ($signature !== $computedSignature) {
        Log::error('Invalid Paystack webhook signature');
        abort(403);
    }

    $payload = $request->json()->all();

    // Handle successful charge
    if ($payload['event'] === 'charge.success') {
        $reference = $payload['data']['reference'];
        Payment::where('reference', $reference)->update(['status' => 'success']);
    }

    return response()->json(['status' => 'success']);
}
}