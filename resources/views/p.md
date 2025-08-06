<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xvilations Payment</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        .payment-method {
            transition: all 0.2s ease;
        }
        .payment-method:hover {
            transform: translateY(-2px);
        }
        .payment-method.active {
            border-color: #3b82f6;
            box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2);
        }
        .payment-section {
            display: none;
        }
        .payment-section.active {
            display: block;
            animation: fadeIn 0.3s ease;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <!-- Header -->
            <div class="bg-blue-600 py-4 px-6">
                <div class="flex items-center justify-between">
                    <h1 class="text-xl font-bold text-white">Xvilations Payment</h1>
                    <i class="fas fa-lock text-white text-2xl"></i>
                </div>
                <p class="text-blue-100 mt-1">Secure payment processing</p>
            </div>
            
            <!-- Payment Form -->
            <div class="p-6">
                <!-- Amount Input -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount (KES)</label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">KES</span>
                        </div>
                        <input type="number" id="amount" min="1" required
                            class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-14 py-3 border-gray-300 rounded-md text-lg font-medium"
                            placeholder="0">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span class="text-gray-500">.00</span>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Methods Row -->
                <div class="mb-6">
                    <h3 class="text-sm font-medium text-gray-700 mb-2">Select Payment Method</h3>
                    <div class="grid grid-cols-3 gap-3">
                        <!-- Card -->
                        <div id="cardMethod" class="payment-method active border border-blue-300 rounded-lg p-3 cursor-pointer bg-white text-center">
                            <div class="bg-blue-100 p-2 rounded-full inline-block mb-2">
                                <i class="far fa-credit-card text-blue-600 text-xl"></i>
                            </div>
                            <p class="text-xs font-medium">Card</p>
                        </div>
                        
                        <!-- M-Pesa -->
                        <div id="mpesaMethod" class="payment-method border border-gray-200 rounded-lg p-3 cursor-pointer bg-white text-center">
                            <div class="bg-green-100 p-2 rounded-full inline-block mb-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/1/15/M-PESA_LOGO-01.svg" alt="M-Pesa" class="h-5 mx-auto">
                            </div>
                            <p class="text-xs font-medium">M-Pesa</p>
                        </div>
                        
                        <!-- Airtel Money -->
                        <div id="airtelMethod" class="payment-method border border-gray-200 rounded-lg p-3 cursor-pointer bg-white text-center">
                            <div class="bg-red-100 p-2 rounded-full inline-block mb-2">
                                <img src="https://upload.wikimedia.org/wikipedia/commons/d/d2/Airtel-Money-Logo.png" alt="Airtel Money" class="h-5 mx-auto">
                            </div>
                            <p class="text-xs font-medium">Airtel Money</p>
                        </div>
                    </div>
                </div>
                
                <!-- Payment Sections -->
                <div class="payment-sections">
                    <!-- Card Payment Section -->
                    <div id="cardSection" class="payment-section active">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Card Number</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="far fa-credit-card text-gray-400"></i>
                                    </div>
                                    <input type="text" id="cardNumber" 
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-10 py-3 border-gray-300 rounded-md"
                                        placeholder="4242 4242 4242 4242" autocomplete="cc-number">
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                    <input type="text" id="cardExpiry" 
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full py-3 border-gray-300 rounded-md"
                                        placeholder="MM/YY" autocomplete="cc-exp">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">CVV</label>
                                    <div class="relative rounded-md shadow-sm">
                                        <input type="text" id="cardCvv" 
                                            class="focus:ring-blue-500 focus:border-blue-500 block w-full py-3 border-gray-300 rounded-md"
                                            placeholder="123" autocomplete="cc-csc">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <i class="fas fa-question-circle text-gray-400" title="3 digits on back of card"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <button id="payWithCard" 
                                class="w-full mt-4 flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-blue-600 hover:bg-blue-700">
                                <i class="fas fa-lock mr-2"></i> Pay KES <span id="cardAmountDisplay">0.00</span>
                            </button>
                        </div>
                    </div>
                    
                    <!-- M-Pesa Payment Section -->
                    <div id="mpesaSection" class="payment-section">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="bg-green-100 p-2 rounded-full">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/15/M-PESA_LOGO-01.svg" alt="M-Pesa" class="h-6">
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Pay with M-Pesa</h3>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">+254</span>
                                    </div>
                                    <input type="tel" id="mpesaPhone" 
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-16 py-3 border-gray-300 rounded-md"
                                        placeholder="7XX XXX XXX">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">We'll send a payment request to this number</p>
                            </div>
                            
                            <button id="payWithMpesa" 
                                class="w-full mt-4 flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-green-600 hover:bg-green-700">
                                Request M-Pesa Payment
                            </button>
                        </div>
                    </div>
                    
                    <!-- Airtel Money Payment Section -->
                    <div id="airtelSection" class="payment-section">
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3 mb-4">
                                <div class="bg-red-100 p-2 rounded-full">
                                    <img src="https://upload.wikimedia.org/wikipedia/commons/d/d2/Airtel-Money-Logo.png" alt="Airtel Money" class="h-6">
                                </div>
                                <h3 class="text-lg font-medium text-gray-900">Pay with Airtel Money</h3>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                                <div class="relative rounded-md shadow-sm">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-gray-500">+254</span>
                                    </div>
                                    <input type="tel" id="airtelPhone" 
                                        class="focus:ring-blue-500 focus:border-blue-500 block w-full pl-16 py-3 border-gray-300 rounded-md"
                                        placeholder="7XX XXX XXX">
                                </div>
                                <p class="mt-1 text-sm text-gray-500">We'll send a payment request to this number</p>
                            </div>
                            
                            <button id="payWithAirtel" 
                                class="w-full mt-4 flex justify-center items-center py-3 px-4 border border-transparent rounded-md shadow-sm text-lg font-medium text-white bg-red-600 hover:bg-red-700">
                                Request Airtel Payment
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Processing Section (Hidden Initially) -->
                <div id="processingSection" class="hidden text-center py-8">
                    <div id="paymentSpinner" class="animate-spin rounded-full h-16 w-16 border-b-2 border-blue-500 mx-auto mb-4"></div>
                    <h3 id="paymentStatusText" class="text-lg font-medium text-gray-900">Processing Payment</h3>
                    <p id="paymentStatusSubtext" class="mt-2 text-sm text-gray-500">Please wait while we process your payment</p>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="border-t border-gray-200 px-6 py-4">
                <div class="flex justify-center space-x-4">
                    <i class="fab fa-cc-visa text-gray-400 text-xl"></i>
                    <i class="fab fa-cc-mastercard text-gray-400 text-xl"></i>
                    <img src="https://upload.wikimedia.org/wikipedia/commons/1/15/M-PESA_LOGO-01.svg" alt="M-Pesa" class="h-5">
                    <img src="https://upload.wikimedia.org/wikipedia/commons/d/d2/Airtel-Money-Logo.png" alt="Airtel Money" class="h-5">
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // DOM Elements
            const amountInput = document.getElementById('amount');
            
            // Payment Methods
            const cardMethod = document.getElementById('cardMethod');
            const mpesaMethod = document.getElementById('mpesaMethod');
            const airtelMethod = document.getElementById('airtelMethod');
            
            // Payment Sections
            const cardSection = document.getElementById('cardSection');
            const mpesaSection = document.getElementById('mpesaSection');
            const airtelSection = document.getElementById('airtelSection');
            
            // Payment Buttons
            const payWithCard = document.getElementById('payWithCard');
            const payWithMpesa = document.getElementById('payWithMpesa');
            const payWithAirtel = document.getElementById('payWithAirtel');
            const cardAmountDisplay = document.getElementById('cardAmountDisplay');
            
            // Processing Section
            const processingSection = document.getElementById('processingSection');
            const paymentSpinner = document.getElementById('paymentSpinner');
            const paymentStatusText = document.getElementById('paymentStatusText');
            const paymentStatusSubtext = document.getElementById('paymentStatusSubtext');
            
            // Payment Data
            let paymentData = {
                amount: 0,
                payment_method: 'card',
                currency: 'KES'
            };
            
            // Update amount display
            amountInput.addEventListener('input', function() {
                const amount = this.value || '0';
                cardAmountDisplay.textContent = parseFloat(amount).toFixed(2);
            });
            
            // Switch between payment methods
            function setActiveMethod(method) {
                // Update UI
                cardMethod.classList.remove('active', 'border-blue-300');
                mpesaMethod.classList.remove('active', 'border-blue-300');
                airtelMethod.classList.remove('active', 'border-blue-300');
                cardSection.classList.remove('active');
                mpesaSection.classList.remove('active');
                airtelSection.classList.remove('active');
                
                // Activate selected method
                if (method === 'card') {
                    cardMethod.classList.add('active', 'border-blue-300');
                    cardSection.classList.add('active');
                    paymentData.payment_method = 'card';
                } 
                else if (method === 'mpesa') {
                    mpesaMethod.classList.add('active', 'border-blue-300');
                    mpesaSection.classList.add('active');
                    paymentData.payment_method = 'mpesa';
                } 
                else if (method === 'airtel') {
                    airtelMethod.classList.add('active', 'border-blue-300');
                    airtelSection.classList.add('active');
                    paymentData.payment_method = 'airtel';
                }
            }
            
            // Method selection handlers
            cardMethod.addEventListener('click', () => setActiveMethod('card'));
            mpesaMethod.addEventListener('click', () => setActiveMethod('mpesa'));
            airtelMethod.addEventListener('click', () => setActiveMethod('airtel'));
            
            // Payment handlers
            payWithCard.addEventListener('click', processCardPayment);
            payWithMpesa.addEventListener('click', processMpesaPayment);
            payWithAirtel.addEventListener('click', processAirtelPayment);
            
            function validateAmount() {
                const amount = amountInput.value;
                if (!amount || amount < 1) {
                    alert('Please enter a valid amount');
                    return false;
                }
                paymentData.amount = amount * 100; // Convert to cents
                return true;
            }
            
            function processCardPayment() {
                if (!validateAmount()) return;
                
                const cardNumber = document.getElementById('cardNumber').value;
                const cardExpiry = document.getElementById('cardExpiry').value;
                const cardCvv = document.getElementById('cardCvv').value;
                
                if (!cardNumber || !cardExpiry || !cardCvv) {
                    alert('Please fill in all card details');
                    return;
                }
                
                paymentData.card = {
                    number: cardNumber.replace(/\s+/g, ''),
                    cvv: cardCvv,
                    expiry_month: cardExpiry.split('/')[0],
                    expiry_year: cardExpiry.split('/')[1]
                };
                
                initiatePayment();
            }
            
            function processMpesaPayment() {
                if (!validateAmount()) return;
                
                const phone = document.getElementById('mpesaPhone').value;
                if (!phone || phone.length < 9) {
                    alert('Please enter a valid phone number');
                    return;
                }
                
                paymentData.phone = '254' + phone.substring(phone.length - 9);
                initiatePayment();
            }
            
            function processAirtelPayment() {
                if (!validateAmount()) return;
                
                const phone = document.getElementById('airtelPhone').value;
                if (!phone || phone.length < 9) {
                    alert('Please enter a valid phone number');
                    return;
                }
                
                paymentData.phone = '254' + phone.substring(phone.length - 9);
                initiatePayment();
            }
            
            function initiatePayment() {
                // Generate reference
                paymentData.reference = 'pay_' + Math.floor(Math.random() * 1000000000) + 1;
                
                // Show processing
                cardSection.classList.remove('active');
                mpesaSection.classList.remove('active');
                airtelSection.classList.remove('active');
                processingSection.classList.remove('hidden');
                
                // Make API call
                fetch('/process-payment', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(paymentData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status) {
                        if (data.payment_method === 'mpesa' || data.payment_method === 'airtel') {
                            // For mobile payments, poll for status
                            paymentStatusText.textContent = 'Awaiting Payment';
                            paymentStatusSubtext.textContent = 'Please complete the payment on your phone';
                            checkPaymentStatus(data.reference);
                        } else {
                            // For card payments, redirect immediately
                            window.location.href = '/payment-success?reference=' + data.reference;
                        }
                    } else {
                        showPaymentError(data.message || 'Payment failed');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showPaymentError('An error occurred while processing your payment');
                });
            }
            
            // Check payment status (for mobile payments)
            function checkPaymentStatus(reference) {
                let attempts = 0;
                const maxAttempts = 20;
                const interval = 3000; // 3 seconds
                
                const checkStatus = setInterval(() => {
                    attempts++;
                    
                    fetch(`/check-payment-status?reference=${reference}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.status === 'success') {
                                clearInterval(checkStatus);
                                window.location.href = '/payment-success?reference=' + reference;
                            } else if (data.status === 'failed') {
                                clearInterval(checkStatus);
                                showPaymentError(data.message || 'Payment failed');
                            } else if (attempts >= maxAttempts) {
                                clearInterval(checkStatus);
                                showPaymentError('Payment timeout. Please check your mobile money account.');
                            }
                        })
                        .catch(error => {
                            console.error('Error checking status:', error);
                            if (attempts >= maxAttempts) {
                                clearInterval(checkStatus);
                                showPaymentError('Unable to verify payment status');
                            }
                        });
                }, interval);
            }
            
            function showPaymentError(message) {
                paymentSpinner.classList.remove('animate-spin', 'border-blue-500');
                paymentSpinner.classList.add('border-red-500');
                paymentSpinner.innerHTML = '<i class="fas fa-exclamation-circle text-red-500 text-4xl"></i>';
                paymentStatusText.textContent = 'Payment Failed';
                paymentStatusSubtext.textContent = message;
                
                // Add retry button
                const retryButton = document.createElement('button');
                retryButton.className = 'mt-4 w-full flex justify-center items-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700';
                retryButton.innerHTML = '<i class="fas fa-sync-alt mr-2"></i> Try Again';
                retryButton.onclick = function() {
                    processingSection.classList.add('hidden');
                    setActiveMethod(paymentData.payment_method);
                };
                
                paymentStatusSubtext.after(retryButton);
            }
        });
    </script>
</body>
</html>