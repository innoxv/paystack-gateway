<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController; 



Route::get('/', function () {
    return view('payment');
});



// This part should be used while using the md in controllers and views (bugs) - This approach was to eliminate the paystack UI
// Paystack 
Route::post('/process-payment', [PaymentController::class, 'processPayment'])->name('process.payment');
Route::get('/check-payment-status', [PaymentController::class, 'checkPaymentStatus'])->name('check.payment.status');

// webhooks
Route::post('/paystack/webhook', [PaymentController::class, 'handleWebhook']);