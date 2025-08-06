<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Yabacon\Paystack;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    public function processCard(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1', 'email' => 'required|email']);
        $paystack = new Paystack(env('PAYSTACK_SECRET_KEY'));
        $data = [
            'amount' => $request->input('amount') * 100,
            'email' => $request->input('email'),
            'reference' => 'card_' . time(),
            'currency' => 'KES',
        ];
        try {
            $response = $paystack->transaction->initialize($data);
            return redirect($response->data->authorization_url);
        } catch (\Exception $e) {
            Log::error('Paystack Error: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Payment initiation failed.']);
        }
    }
}