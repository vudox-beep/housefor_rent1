<?php

namespace App\Http\Controllers\Payments;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Payment;
use Illuminate\Support\Facades\Auth;

class LencoPaymentController extends Controller
{
    private $baseUrl;
    private $apiKey;
    private $webhookSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.lenco.base_url'), '/');
        $this->apiKey = config('services.lenco.key');
        $this->webhookSecret = config('services.lenco.webhook_secret');
    }

    private function getAuthorizationHeader(): string
    {
        $key = (string) $this->apiKey;
        $normalized = strtolower($key);

        if ($key !== '' && !str_starts_with($normalized, 'bearer ')) {
            return 'Bearer ' . $key;
        }

        return $key;
    }

    private function normalizeMobileMoneyPhone(string $phone, string $countryIso): string
    {
        $digits = preg_replace('/\D+/', '', $phone);
        $countryCode = $countryIso === 'mw' ? '265' : '260';

        if (str_starts_with($digits, $countryCode)) {
            $digits = substr($digits, strlen($countryCode));
        }

        if (str_starts_with($digits, '0')) {
            $digits = ltrim($digits, '0');
        }

        return $digits;
    }

    public function debugMobileMoneyCollection(Request $request)
    {
        $validated = $request->validate([
            'operator' => 'required|in:airtel,mtn,tnm',
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'country' => 'required|in:zm,mw',
        ]);

        $currency = $validated['country'] === 'mw' ? 'MWK' : 'ZMW';
        $operator = $validated['country'] === 'mw' && $validated['operator'] === 'mtn'
            ? 'tnm'
            : $validated['operator'];

        $normalizedPhone = $this->normalizeMobileMoneyPhone($validated['phone_number'], $validated['country']);
        $countryCode = $validated['country'] === 'mw' ? '265' : '260';
        $msisdnPhone = $countryCode . $normalizedPhone;
        $countryCandidates = [$validated['country'], strtoupper($validated['country'])];
        $phoneCandidates = [$normalizedPhone, $msisdnPhone];

        $attempts = [];

        foreach ($countryCandidates as $countryCandidate) {
            foreach ($phoneCandidates as $phoneCandidate) {
                $payload = [
                    'amount' => number_format((float) $validated['amount'], 2, '.', ''),
                    'currency' => $currency,
                    'reference' => 'debug_' . time(),
                    'type' => 'mobile-money',
                    'mobileMoneyDetails' => [
                        'country' => $countryCandidate,
                        'phone' => $phoneCandidate,
                        'operator' => strtolower($operator),
                    ],
                    'bearer' => 'customer',
                ];

                $response = Http::withHeaders([
                    'Authorization' => $this->getAuthorizationHeader(),
                    'Content-Type' => 'application/json',
                    'accept' => 'application/json',
                ])->post($this->baseUrl . '/collections/mobile-money', $payload);

                $attempts[] = [
                    'country' => $countryCandidate,
                    'phone' => $phoneCandidate,
                    'status' => $response->status(),
                    'body' => $response->json(),
                ];
            }
        }

        return response()->json([
            'success' => true,
            'attempts' => $attempts,
        ]);
    }

    public function showPaymentForm(Request $request)
    {
        $amount = $request->query('amount', 20);
        $plan = $request->query('plan', 'gold');
        $method = $request->query('method', 'visa_mastercard');

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Create payment record upfront
        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'currency' => 'ZMW',
            'payment_method' => $method,
            'status' => 'pending',
            'type' => 'subscription',
        ]);

        return view('payments.lenco_form', compact('amount', 'plan', 'method', 'payment'));
    }

    public function verify(Request $request)
    {
        $validated = $request->validate([
            'reference' => 'required|string',
            'payment_public_id' => 'required|string',
            'amount' => 'required|numeric',
            'plan' => 'required|string',
            'method' => 'required|string',
        ]);

        $payment = Payment::where('public_id', $validated['payment_public_id'])->first();
        $user = Auth::user();

        if (!$payment || $payment->user_id !== $user?->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        try {
            // Call Lenco API to verify payment status
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthorizationHeader(),
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ])->get($this->baseUrl . '/collections/status/' . $validated['reference']);

            Log::info('Lenco verify response', [
                'reference' => $validated['reference'],
                'status' => $response->status(),
                'body' => $response->json(),
            ]);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to verify payment status',
                ], 400);
            }

            $data = $response->json();
            $status = data_get($data, 'data.status') ?? data_get($data, 'status');

            // Check if payment was successful
            if (in_array($status, ['successful', 'completed', 'success'])) {
                // Update payment record
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $validated['reference'],
                ]);

                // Update user subscription
                $user->update([
                    'subscription_plan' => 'gold',
                    'subscription_expires_at' => now()->addMonth(),
                ]);

                Log::info('Payment verified and subscription updated', [
                    'payment_public_id' => $validated['payment_public_id'],
                    'user_id' => $user->id,
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment successful! Subscription activated.',
                ]);
            }

            // Payment not yet confirmed
            $payment->update(['status' => 'pending']);

            return response()->json([
                'success' => false,
                'message' => 'Payment status: ' . ($status ?? 'unknown'),
            ], 400);

        } catch (\Exception $e) {
            Log::error('Lenco verify exception', [
                'reference' => $validated['reference'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error verifying payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function validateMobileMoneyAccount(Request $request)
    {
        $validated = $request->validate([
            'operator' => 'required|in:airtel,mtn',
            'phone_number' => 'required|string|min:7|max:15',
        ]);

        $operator = strtolower($validated['operator']);
        
        try {
            // Log the API key being used (first 10 chars only for security)
            $keyPreview = substr($this->apiKey, 0, 10) . '...';
            Log::info('Lenco API validation attempt', [
                'operator' => $operator,
                'phone' => $validated['phone_number'],
                'api_key_preview' => $keyPreview,
                'base_url' => $this->baseUrl,
            ]);

            // Call Lenco API to resolve/validate mobile money account
            $response = Http::withHeaders([
                'Authorization' => $this->getAuthorizationHeader(),
                'Content-Type' => 'application/json',
                'accept' => 'application/json',
            ])->post($this->baseUrl . '/resolve/mobile-money', [
                'operator' => $operator,
                'phone' => $validated['phone_number'],
            ]);

            $responseData = $response->json();
            
            Log::info('Lenco mobile money validation response', [
                'operator' => $operator,
                'phone' => $validated['phone_number'],
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            if ($response->successful()) {
                // Check different possible response structures
                $accountData = data_get($responseData, 'data') 
                    ?? data_get($responseData, 'account')
                    ?? $responseData;

                if ($accountData && is_array($accountData) && !empty($accountData)) {
                    return response()->json([
                        'success' => true,
                        'message' => 'Account verified',
                        'account' => [
                            'operator' => data_get($accountData, 'operator') ?? $operator,
                            'accountName' => data_get($accountData, 'accountName') ?? data_get($accountData, 'name') ?? 'Account Holder',
                            'phone' => data_get($accountData, 'phone') ?? $validated['phone_number'],
                            'country' => data_get($accountData, 'country') ?? 'Unknown',
                        ],
                    ]);
                }
            }

            // Handle 401 Unauthorized specifically
            if ($response->status() === 401) {
                Log::error('Lenco API unauthorized - invalid key', [
                    'operator' => $operator,
                    'status' => 401,
                    'response' => $responseData,
                ]);
                
                return response()->json([
                    'success' => false,
                    'message' => 'API authentication failed. Please verify your Lenco API credentials in .env',
                ], 401);
            }

            // Log failed response for debugging
            Log::warning('Lenco mobile money validation failed', [
                'operator' => $operator,
                'phone' => $validated['phone_number'],
                'status' => $response->status(),
                'response' => $responseData,
            ]);

            $errorMessage = data_get($responseData, 'message') 
                ?? data_get($responseData, 'error')
                ?? 'Unable to verify account. Please check the details and try again.';

            return response()->json([
                'success' => false,
                'message' => $errorMessage,
            ], 400);
        } catch (\Exception $e) {
            Log::error('Lenco mobile money validation exception', [
                'operator' => $operator,
                'phone' => $validated['phone_number'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error validating account: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function initiateMobileMoneyPayment(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|integer',
            'operator' => 'required|in:airtel,mtn',
            'phone_number' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'country' => 'required|in:zm,mw',
        ]);

        $payment = Payment::find($validated['payment_id']);
        $user = Auth::user();

        if (!$payment || $payment->user_id !== $user?->id) {
            return response()->json([
                'success' => false,
                'message' => 'Payment not found',
            ], 404);
        }

        try {
            // Call Lenco API to initiate mobile money collection
            $currency = $validated['country'] === 'mw' ? 'MWK' : 'ZMW';
            $operator = $validated['country'] === 'mw' && $validated['operator'] === 'mtn'
                ? 'tnm'
                : $validated['operator'];

            $normalizedPhone = $this->normalizeMobileMoneyPhone($validated['phone_number'], $validated['country']);
            $countryCode = $validated['country'] === 'mw' ? '265' : '260';
            $localPhone = '0' . $normalizedPhone;
            $msisdnPhone = $countryCode . $normalizedPhone;
            $countryCandidates = [$validated['country'], strtoupper($validated['country'])];

            $payloadBase = [
                'amount' => number_format((float) $validated['amount'], 2, '.', ''),
                'currency' => $currency,
                'reference' => 'payment_' . $payment->id . '_' . time(),
                'type' => 'mobile-money',
                'mobileMoneyDetails' => [
                    'country' => $validated['country'],
                    'operator' => strtolower($operator),
                ],
                'bearer' => 'customer',
            ];

            Log::info('Initiating Lenco mobile money payment', [
                'payment_id' => $payment->id,
                'operator' => $operator,
                'phone' => $validated['phone_number'],
                'normalized_phone' => $normalizedPhone,
                'msisdn_phone' => $msisdnPhone,
                'amount' => $validated['amount'],
            ]);

            // Try Lenco endpoints for charging (primary: official mobile money collections endpoint)
            $endpoints = [
                '/collections/mobile-money',
                '/collections',
                '/charge',
                '/initiate-charge',
                '/charges',
                '/payments',
            ];

            $response = null;
            $lastError = null;
            $stopAfterCollections = false;

            foreach ($endpoints as $endpoint) {
                try {
                    $url = $this->baseUrl . $endpoint;
                    Log::info('Trying Lenco endpoint', ['url' => $url]);

                    if ($endpoint === '/collections/mobile-money') {
                        $phoneCandidates = [$localPhone, $normalizedPhone, $msisdnPhone];

                        foreach ($countryCandidates as $countryCandidate) {
                            foreach ($phoneCandidates as $phoneCandidate) {
                                $payload = $payloadBase;
                                $payload['mobileMoneyDetails']['phone'] = $phoneCandidate;
                                $payload['mobileMoneyDetails']['country'] = $countryCandidate;

                                $response = Http::withHeaders([
                                    'Authorization' => $this->getAuthorizationHeader(),
                                    'Content-Type' => 'application/json',
                                    'accept' => 'application/json',
                                ])->post($url, $payload);

                                if ($response->successful() || $response->status() === 201) {
                                    Log::info('Lenco endpoint successful', [
                                        'endpoint' => $endpoint,
                                        'status' => $response->status(),
                                    ]);
                                    break 2;
                                }

                                $lastError = ['endpoint' => $endpoint, 'status' => $response->status(), 'body' => $response->json()];
                                Log::warning('Lenco endpoint failed', $lastError);

                                if ($response->status() !== 400) {
                                    break 2;
                                }
                            }
                        }

                        if ($response && $response->status() === 400) {
                            $stopAfterCollections = true;
                        }
                    } else {
                        $payload = $payloadBase;

                        $response = Http::withHeaders([
                            'Authorization' => $this->getAuthorizationHeader(),
                            'Content-Type' => 'application/json',
                            'accept' => 'application/json',
                        ])->post($url, $payload);
                    }

                    if ($response->successful() || $response->status() === 201) {
                        Log::info('Lenco endpoint successful', [
                            'endpoint' => $endpoint,
                            'status' => $response->status(),
                        ]);
                        break;
                    }

                    if (!$lastError || $lastError['endpoint'] !== $endpoint) {
                        $lastError = ['endpoint' => $endpoint, 'status' => $response->status(), 'body' => $response->json()];
                        Log::warning('Lenco endpoint failed', $lastError);
                    }

                    if ($stopAfterCollections) {
                        break;
                    }

                } catch (\Exception $e) {
                    Log::warning('Lenco endpoint error', ['endpoint' => $endpoint, 'error' => $e->getMessage()]);
                    continue;
                }
            }

            if (!$response || (!$response->successful() && $response->status() !== 201)) {
                Log::error('All Lenco endpoints failed', [
                    'last_error' => $lastError,
                ]);

                $userMessage = 'Failed to initiate payment. Approval message could not be sent.';

                if ($response) {
                    $responseData = $response->json();
                    $apiMessage = data_get($responseData, 'message') ?? data_get($responseData, 'error');

                    if ($response->status() === 401) {
                        $userMessage = 'API authentication failed with Lenco. Please check your Lenco API key and base URL.';
                    } elseif ($apiMessage) {
                        $userMessage = $apiMessage;
                    }
                }

                return response()->json([
                    'success' => false,
                    'message' => $userMessage,
                ], 400);
            }

            $data = $response->json();
            $reference = data_get($data, 'data.reference') 
                ?? data_get($data, 'reference')
                ?? data_get($data, 'data.id');

            // Update payment with transaction reference
            if ($reference) {
                $payment->update([
                    'transaction_id' => $reference,
                    'status' => 'pending',
                ]);
            }

            Log::info('Mobile money payment initiated successfully', [
                'payment_id' => $payment->id,
                'reference' => $reference,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Approval request sent to ' . $validated['phone_number'] . '. Check your phone for the SMS/USSD prompt.',
                'reference' => $reference,
                'payment_id' => $payment->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Mobile money payment initiation exception', [
                'payment_id' => $validated['payment_id'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error initiating payment: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->all();

        Log::info('Lenco webhook received', $payload);

        // Verify webhook signature if provided
        if ($this->webhookSecret && $request->hasHeader('X-Lenco-Signature')) {
            $signature = $request->header('X-Lenco-Signature');
            $computed = hash_hmac('sha256', json_encode($payload), $this->webhookSecret);
            
            if (!hash_equals($signature, $computed)) {
                Log::warning('Lenco webhook signature mismatch');
                return response()->json(['error' => 'Invalid signature'], 403);
            }
        }

        // Extract payment status from Lenco webhook
        $event = data_get($payload, 'event');
        $status = data_get($payload, 'data.status');
        $reference = data_get($payload, 'data.reference');
        $txnId = data_get($payload, 'data.id');
        $metadata = data_get($payload, 'data.metadata');

        // Find payment by reference or metadata
        $payment = null;
        if ($metadata && isset($metadata['payment_id'])) {
            $payment = Payment::find($metadata['payment_id']);
        } elseif ($reference && strpos($reference, 'payment_') === 0) {
            preg_match('/payment_(\d+)_/', $reference, $matches);
            if (isset($matches[1])) {
                $payment = Payment::find($matches[1]);
            }
        }

        if ($payment) {
            // Map Lenco status to our status
            if (in_array($status, ['successful', 'completed', 'success'])) {
                $payment->update([
                    'status' => 'completed',
                    'transaction_id' => $txnId ?? $payment->transaction_id,
                ]);

                // Update user subscription on successful payment
                $user = $payment->user;
                if ($user) {
                    $user->update([
                        'subscription_plan' => 'gold',
                        'subscription_expires_at' => now()->addMonth(),
                    ]);

                    Log::info('User subscription updated', ['user_id' => $user->id]);
                }
            } elseif (in_array($status, ['failed', 'cancelled'])) {
                $payment->update([
                    'status' => 'failed',
                    'transaction_id' => $txnId ?? $payment->transaction_id,
                ]);
            }
        }

        return response()->json(['received' => true]);
    }

    public function return(Request $request)
    {
        $paymentId = $request->query('payment_id');
        $status = $request->query('status');

        if (!$paymentId) {
            return redirect()->route('dealer.dashboard')
                ->with('warning', 'Payment reference not found.');
        }

        $payment = Payment::find($paymentId);

        if (!$payment) {
            return redirect()->route('dealer.dashboard')
                ->with('warning', 'Payment not found.');
        }

        // If explicitly marked as failed in query
        if ($status === 'failed') {
            $payment->update(['status' => 'failed']);
            return redirect()->route('dealer.subscription')
                ->with('error', 'Payment was cancelled or declined. Please try again.');
        }

        // Check current status - Lenco webhook should have updated it
        if ($payment->status === 'completed') {
            return redirect()->route('dealer.dashboard')
                ->with('success', 'Payment successful! Your Gold subscription is now active.');
        }

        // Still pending - wait for webhook
        if ($payment->status === 'pending') {
            return redirect()->route('dealer.dashboard')
                ->with('info', 'Payment is being processed. You will be updated once confirmed.');
        }

        if ($payment->status === 'failed') {
            return redirect()->route('dealer.subscription')
                ->with('error', 'Payment failed. Please try a different payment method.');
        }

        return redirect()->route('dealer.dashboard')
            ->with('info', 'Payment status: ' . $payment->status);
    }
}
