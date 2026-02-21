<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Complete Payment - {{ config('app.name', 'Laravel') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="{{ asset('css/base.css') }}">
    <style>
        body {
            background-color: #F3F4F6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .navbar {
            background-color: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            padding: 1rem 0;
        }
        
        .navbar-content {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 800;
            font-size: 1.25rem;
            color: var(--primary-color);
            text-decoration: none;
        }
        
        .payment-container {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1.5rem;
        }
        
        .payment-card {
            background: white;
            border-radius: 1rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            width: 100%;
            max-width: 480px;
            overflow: hidden;
        }
        
        .card-header {
            background-color: #FFF7ED; /* Light orange bg */
            padding: 1.5rem;
            border-bottom: 1px solid #FED7AA;
            text-align: center;
        }
        
        .plan-badge {
            background-color: var(--primary-color);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: inline-block;
            margin-bottom: 0.5rem;
        }
        
        .amount-display {
            font-size: 2.25rem;
            font-weight: 800;
            color: var(--dark-text);
            margin: 0.5rem 0;
        }
        
        .currency {
            font-size: 1rem;
            color: var(--muted-text);
            font-weight: 500;
            vertical-align: super;
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .info-box {
            background-color: #F0F9FF;
            border-left: 4px solid #0EA5E9;
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .info-text {
            color: #0369A1;
            font-size: 0.9rem;
            line-height: 1.5;
        }
        
        .form-group {
            margin-bottom: 1.25rem;
        }
        
        .form-label {
            display: block;
            font-weight: 600;
            color: var(--dark-text);
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        
        .form-select, .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #D1D5DB;
            border-radius: 0.5rem;
            font-size: 1rem;
            transition: all 0.2s;
        }
        
        .form-select:focus, .form-input:focus {
            border-color: var(--primary-color);
            outline: none;
            box-shadow: 0 0 0 3px rgba(217, 119, 6, 0.1);
        }
        
        .btn-pay {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-weight: 700;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn-pay:hover {
            background-color: var(--primary-hover);
        }
        
        .btn-pay:disabled {
            opacity: 0.7;
            cursor: not-allowed;
        }
        
        .secure-badge {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            margin-top: 1.5rem;
            color: var(--muted-text);
            font-size: 0.8rem;
        }
        
        .back-link {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .back-link a {
            color: var(--muted-text);
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .back-link a:hover {
            color: var(--dark-text);
        }

        /* Phone Input Group */
        .phone-input-group {
            display: flex;
            gap: 0.5rem;
        }
        
        .phone-prefix {
            width: 80px;
            background-color: #F9FAFB;
            color: #6B7280;
            text-align: center;
        }

        /* Success/Error Messages */
        .status-message {
            padding: 1rem;
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            display: none;
            font-size: 0.9rem;
        }
        
        .status-success {
            background-color: #DCFCE7;
            color: #166534;
            border: 1px solid #BBF7D0;
        }
        
        .status-error {
            background-color: #FEE2E2;
            color: #991B1B;
            border: 1px solid #FECACA;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="{{ route('home') }}" class="logo">
                <svg width="24" height="24" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                HouseForRent
            </a>
            <div style="font-size: 0.9rem; color: #6B7280;">
                Secure Payment
            </div>
        </div>
    </nav>

    <div class="payment-container">
        <div class="payment-card">
            <div class="card-header">
                <span class="plan-badge">{{ ucfirst($plan) }} Plan</span>
                <div class="amount-display">
                    <span class="currency">ZMW</span> {{ number_format($amount, 2) }}
                </div>
                <p style="color: #9A3412; font-size: 0.9rem; margin: 0;">{{ $method === 'visa_mastercard' ? 'Card Payment' : 'Mobile Money Payment' }}</p>
            </div>

            <div class="card-body">
                <!-- Status Messages -->
                <div id="validationError" class="status-message status-error">
                    <div style="display: flex; gap: 0.5rem;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <span id="validationErrorText"></span>
                    </div>
                </div>

                <div id="validationSuccess" class="status-message status-success">
                    <div style="display: flex; gap: 0.5rem;">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        <div>
                            <strong>Account Verified</strong>
                            <div id="accountNameDisplay" style="margin-top: 0.25rem;"></div>
                        </div>
                    </div>
                </div>

                <!-- Info Box -->
                <div class="info-box">
                    <div style="display: flex; gap: 0.75rem;">
                        <svg width="24" height="24" fill="none" stroke="#0284C7" viewBox="0 0 24 24" style="flex-shrink: 0;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="info-text">
                            @if(in_array($method, ['airtel_money', 'mtn_money']))
                                Please verify your phone number below. Once verified, you'll receive a prompt on your phone to approve the payment.
                            @else
                                Click "Pay Now" to open our secure card payment window. Supports Visa and Mastercard.
                            @endif
                        </div>
                    </div>
                </div>

                @if(in_array($method, ['airtel_money', 'mtn_money']))
                    <div id="mobileValidationForm">
                        <!-- Country -->
                        <div class="form-group">
                            <label class="form-label">Country</label>
                            <select id="countrySelect" onchange="updatePhoneFormat()" class="form-select">
                                <option value="">Select country...</option>
                                <option value="260" data-name="Zambia" data-format="0971234567">🇿🇲 Zambia (+260)</option>
                                <option value="265" data-name="Malawi" data-format="0881234567">🇲🇼 Malawi (+265)</option>
                                <option value="263" data-name="Zimbabwe" data-format="0771234567">🇿🇼 Zimbabwe (+263)</option>
                                <option value="256" data-name="Uganda" data-format="0701234567">🇺🇬 Uganda (+256)</option>
                                <option value="254" data-name="Kenya" data-format="0712345678">🇰🇪 Kenya (+254)</option>
                                <option value="255" data-name="Tanzania" data-format="0652345678">🇹🇿 Tanzania (+255)</option>
                                <option value="258" data-name="Mozambique" data-format="0821234567">🇲🇿 Mozambique (+258)</option>
                                <option value="27" data-name="South Africa" data-format="0712345678">🇿🇦 South Africa (+27)</option>
                                <option value="234" data-name="Nigeria" data-format="0812345678">🇳🇬 Nigeria (+234)</option>
                                <option value="233" data-name="Ghana" data-format="0501234567">🇬🇭 Ghana (+233)</option>
                            </select>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label class="form-label">{{ $method === 'airtel_money' ? 'Airtel' : 'MTN' }} Number</label>
                            <div class="phone-input-group">
                                <input type="text" id="countryPrefix" class="form-input phone-prefix" readonly placeholder="+260">
                                <input type="text" id="phoneNumber" class="form-input" placeholder="e.g. 971234567">
                            </div>
                            <small id="countryHint" style="color: #6B7280; display: block; margin-top: 0.25rem; font-size: 0.8rem;"></small>
                        </div>

                        <button type="button" onclick="validateMobileAccount()" class="btn-pay" style="background-color: #4F46E5; margin-bottom: 1rem;">
                            Verify Account
                        </button>
                    </div>

                    <!-- Pay Button (Hidden initially) -->
                    <div id="payButtonContainer" style="display: none;">
                        <button type="button" id="payButtonMobileMoney" class="btn-pay">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                            Confirm Payment (ZMW {{ $amount }})
                        </button>
                    </div>
                @else
                    <!-- Card Payment -->
                    <button type="button" id="payButtonCard" class="btn-pay">
                        <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Pay ZMW {{ $amount }} Now
                    </button>
                @endif

                <div class="secure-badge">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Secured by Lenco
                </div>

                <div class="back-link">
                    <a href="{{ route('dealer.subscription') }}">← Cancel and return</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://pay.lenco.co/js/v1/inline.js"></script>
    <script>
        const countryFormats = {
            '260': { name: 'Zambia', format: /^0?9\d{8}$|^9\d{8}$/, prefix: '+260', iso: 'zm' },
            '265': { name: 'Malawi', format: /^0?8\d{8}$|^8\d{8}$/, prefix: '+265', iso: 'mw' },
            '263': { name: 'Zimbabwe', format: /^0?7\d{8}$|^7\d{8}$/, prefix: '+263', iso: '' },
            '256': { name: 'Uganda', format: /^0?7\d{8}$|^7\d{8}$/, prefix: '+256', iso: '' },
            '254': { name: 'Kenya', format: /^0?7\d{9}$|^7\d{9}$/, prefix: '+254', iso: '' },
            '255': { name: 'Tanzania', format: /^0?6\d{8}$|^6\d{8}$/, prefix: '+255', iso: '' },
            '258': { name: 'Mozambique', format: /^0?8\d{8}$|^8\d{8}$/, prefix: '+258', iso: '' },
            '27': { name: 'South Africa', format: /^0?7\d{8}$|^7\d{8}$/, prefix: '+27', iso: '' },
            '234': { name: 'Nigeria', format: /^0?8\d{9}$|^8\d{9}$/, prefix: '+234', iso: '' },
            '233': { name: 'Ghana', format: /^0?5\d{8}$|^5\d{8}$/, prefix: '+233', iso: '' },
        };

        function updatePhoneFormat() {
            const countrySelect = document.getElementById('countrySelect');
            const countryCode = countrySelect.value;
            const countryPrefix = document.getElementById('countryPrefix');
            const countryHint = document.getElementById('countryHint');
            const validationError = document.getElementById('validationError');
            const validationSuccess = document.getElementById('validationSuccess');
            const payButtonContainer = document.getElementById('payButtonContainer');

            validationError.style.display = 'none';
            validationSuccess.style.display = 'none';
            if(payButtonContainer) payButtonContainer.style.display = 'none';

            if (!countryCode) {
                countryPrefix.value = '';
                countryHint.textContent = '';
                return;
            }

            const format = countryFormats[countryCode];
            countryPrefix.value = format.prefix;
            countryHint.textContent = 'Format example: ' + countrySelect.options[countrySelect.selectedIndex].getAttribute('data-format');
        }

        function formatPhoneNumber(phone, countryCode) {
            let cleaned = phone.replace(/\D/g, '');
            if (!cleaned) return '';
            if (cleaned.startsWith(countryCode)) return '+' + cleaned;
            if (cleaned.startsWith('0')) return '+' + countryCode + cleaned.substring(1);
            return '+' + countryCode + cleaned;
        }

        function normalizeToLocal(phone, countryCode) {
            let cleaned = phone.replace(/\D/g, '');
            if (!cleaned) return '';
            if (cleaned.startsWith(countryCode)) return '0' + cleaned.substring(countryCode.length);
            if (cleaned.startsWith('0')) return cleaned;
            return '0' + cleaned;
        }

        function validateMobileAccount() {
            const countrySelect = document.getElementById('countrySelect');
            const phoneInput = document.getElementById('phoneNumber');
            const countryCode = countrySelect.value;
            const phone = phoneInput.value.trim();
            const validationError = document.getElementById('validationError');
            const validationSuccess = document.getElementById('validationSuccess');
            const payButtonContainer = document.getElementById('payButtonContainer');
            const errorText = document.getElementById('validationErrorText');

            validationError.style.display = 'none';
            validationSuccess.style.display = 'none';
            if(payButtonContainer) payButtonContainer.style.display = 'none';

            if (!countryCode) {
                validationError.style.display = 'block';
                errorText.textContent = 'Please select a country first';
                return;
            }

            if (!phone) {
                validationError.style.display = 'block';
                errorText.textContent = 'Please enter a phone number';
                return;
            }

            const formatConfig = countryFormats[countryCode];
            if (!formatConfig?.iso) {
                validationError.style.display = 'block';
                errorText.textContent = 'Selected country is not supported for mobile money payments';
                return;
            }

            const formattedPhone = formatPhoneNumber(phone, countryCode);
            const validateBtn = event.target;
            const originalText = validateBtn.textContent;
            validateBtn.disabled = true;
            validateBtn.textContent = 'Validating...';

            const operator = '{{ $method }}' === 'airtel_money' ? 'airtel' : 'mtn';

            fetch('{{ route("payments.lenco.validate-account") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    operator: operator,
                    phone_number: formattedPhone,
                }),
            })
            .then(response => response.json())
            .then(data => {
                validateBtn.disabled = false;
                validateBtn.textContent = originalText;

                if (data.success && data.account) {
                    validationSuccess.style.display = 'block';
                    document.getElementById('accountNameDisplay').textContent = data.account.accountName + ' (' + data.account.phone + ')';
                    
                    const validatedPhone = data.account?.phone || formattedPhone;
                    window.validatedAccount = {
                        operator: data.account?.operator || operator,
                        phone: formatPhoneNumber(validatedPhone, countryCode),
                        phoneLocal: normalizeToLocal(validatedPhone, countryCode),
                        accountName: data.account.accountName,
                        country: countryCode,
                        countryIso: formatConfig.iso,
                    };

                    if(payButtonContainer) payButtonContainer.style.display = 'block';
                } else {
                    validationError.style.display = 'block';
                    errorText.textContent = data.message || 'Unable to verify account';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                validateBtn.disabled = false;
                validateBtn.textContent = originalText;
                validationError.style.display = 'block';
                errorText.textContent = 'Error: ' + error.message;
            });
        }

        function getPaidWithLenco() {
            const phone = window.validatedAccount?.phoneLocal
                || window.validatedAccount?.phone
                || '{{ auth()->user()->phone ?? "+260" }}';
            const publicKey = '{{ config("services.lenco.secret") }}';

            try {
                LencoPay.getPaid({
                    key: publicKey,
                    reference: 'payment_{{ $payment->public_id }}_' + Date.now(),
                    email: '{{ auth()->user()->email }}',
                    amount: {{ $amount }},
                    currency: "ZMW",
                    channels: ["{{ $method === 'visa_mastercard' ? 'card' : 'mobile-money' }}"],
                    customer: {
                        firstName: "{{ explode(' ', auth()->user()->name)[0] }}",
                        lastName: "{{ count(explode(' ', auth()->user()->name)) > 1 ? explode(' ', auth()->user()->name)[1] : '' }}",
                        phone: phone,
                    },
                    onSuccess: function (response) {
                        verifyPayment(response.reference);
                    },
                    onClose: function () {
                        alert('Payment cancelled.');
                    },
                });
            } catch (error) {
                alert('Error opening payment widget: ' + error.message);
            }
        }

        function verifyPayment(reference) {
            const btn = document.getElementById('payButtonMobileMoney') || document.getElementById('payButtonCard');
            if(btn) {
                btn.disabled = true;
                btn.textContent = 'Verifying...';
            }

            fetch('{{ route("payments.lenco.verify") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    reference: reference,
                    payment_public_id: '{{ $payment->public_id }}',
                    amount: {{ $amount }},
                    plan: '{{ $plan }}',
                    method: '{{ $method }}',
                }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.href = '{{ route("dealer.dashboard") }}';
                } else {
                    alert('Payment verification failed: ' + data.message);
                    window.location.href = '{{ route("dealer.subscription") }}';
                }
            })
            .catch(error => {
                alert('Error verifying payment.');
                window.location.href = '{{ route("dealer.subscription") }}';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const payButtonMobileMoney = document.getElementById('payButtonMobileMoney');
            const payButtonCard = document.getElementById('payButtonCard');

            if (payButtonMobileMoney) {
                payButtonMobileMoney.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (!window.validatedAccount) {
                        alert('Please verify your account first');
                        return;
                    }
                    getPaidWithLenco();
                });
            }

            if (payButtonCard) {
                payButtonCard.addEventListener('click', function(e) {
                    e.preventDefault();
                    getPaidWithLenco();
                });
            }
        });
    </script>
</body>
</html>
