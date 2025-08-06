<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Gateway</title>
    <script src="https://js.paystack.co/v1/inline.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Toastify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css">
    <style>
        /* Custom toast styling */
        .toastify {
            @apply rounded-lg shadow-lg font-medium;
        }
        .toast-success {
            @apply bg-green-500;
        }
        .toast-error {
            @apply bg-red-500;
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 py-4 px-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-medium text-white">Payment Gateway</h1>
                </div>
            </div>
            
            <!-- Payment Form -->
            <div class="p-6">                
                <form id="paymentForm" class="space-y-4">
                    <div>
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount (KES)</label>
                        <div class="relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">KES</span>
                            </div>
                            <input type="number" id="amount" name="amount" min="1" required
                                class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-14 pr-12 py-3 border-gray-300 rounded-md"
                                placeholder="0.00">
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <div class="mt-1 relative rounded-md shadow-sm">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-envelope text-gray-400"></i>
                            </div>
                            <input type="email" id="email" name="email" required
                                class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 py-3 border-gray-300 rounded-md"
                                placeholder="your@email.com">
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                            Pay Now
                        </button>
                    </div>
                </form>
                <div class="mt-6 border-t border-gray-200 pt-4">
                    <div class="flex items-center justify-center space-x-4">
                        <span class="text-xs text-gray-500 font-bold"><i class="fas fa-lock text-grey text-sm"></i> Secure payment by paystack</span>
                    </div>
                    <div class="mt-3 flex justify-center space-x-4 items-center"> 
                        <i class="fab fa-cc-visa text-gray-400 text-2xl"></i>
                        <i class="fab fa-cc-mastercard text-gray-400 text-2xl"></i>
                        <i class="fab fa-cc-amex text-gray-400 text-2xl"></i>
                        <span class="text-gray-500 text-xs align-middle">Mpesa</span> 
                        <span class="text-gray-500 text-xs align-middle">Airtel Money</span> 
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Toastify JS -->
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <script>
        // Custom toast function
        function showToast(message, type = 'success') {
            Toastify({
                text: message,
                duration: 3000,
                close: true,
                gravity: "top",
                position: "center",
                stopOnFocus: true,
                className: type === 'success' ? 'toast-success' : 'toast-error',
            }).showToast();
        }

        document.getElementById('paymentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const email = document.getElementById('email').value;
            const amount = document.getElementById('amount').value;
            
            if (!email || !amount) {
                showToast('Please fill all required fields', 'error');
                return;
            }

            let handler = PaystackPop.setup({
                key: '{{ env('PAYSTACK_PUBLIC_KEY') }}',
                email: email,
                amount: amount * 100,
                currency: 'KES',
                ref: 'txn_' + Math.floor(Math.random() * 1000000000),
                onClose: function() {
                    showToast('Payment was cancelled', 'error');
                },
                callback: function(response) {
                    showToast('Payment successful! Redirecting...');
                    setTimeout(() => {
                        window.location.href = '/payment-success?reference=' + response.reference;
                    }, 2000);
                }
            });
            handler.openIframe();
        });
    </script>
</body>
</html>