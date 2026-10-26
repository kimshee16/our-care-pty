@php
    $adminRegistration = $adminRegistration ?? false;
    $recaptchaSiteKey = $adminRegistration ? null : config('services.recaptcha.site_key');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $adminRegistration ? 'Add a Client' : 'Client Sign Up' }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .register-container {
            max-width: 500px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .register-content {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
        }

        .register-content h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .register-content p {
            color: #666;
            margin-bottom: 30px;
            font-size: 14px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
            font-size: 14px;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            font-family: inherit;
        }

        .password-field {
            position: relative;
        }

        .password-field input {
            padding-right: 44px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 10px;
            transform: translateY(-50%);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            padding: 0;
            color: #555;
            background: transparent;
            border: 0;
            cursor: pointer;
        }

        .password-toggle svg {
            width: 20px;
            height: 20px;
        }

        .password-toggle .eye-closed {
            display: none;
        }

        .password-toggle.is-visible .eye-open {
            display: none;
        }

        .password-toggle.is-visible .eye-closed {
            display: block;
        }

        .form-group .checkbox-label {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            color: #444;
            font-size: 13px;
            line-height: 1.5;
        }

        .checkbox-label input[type="checkbox"] {
            width: auto;
            margin-top: 3px;
            flex: 0 0 auto;
        }

        .recaptcha-group {
            overflow-x: auto;
        }

        .form-group input:focus,
        .form-group select:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .form-row .form-group {
            margin-bottom: 0;
        }

        .register-btn {
            width: 100%;
            padding: 10px 16px;
            background: #674cbf;
            color: #fff;
            border: none;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-link {
            margin-top: 20px;
            color: #666;
            font-size: 14px;
        }

        .login-link a {
            color: #007bff;
            text-decoration: none;
        }

        .login-link a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .form-row {
                grid-template-columns: 1fr;
            }

            .register-content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-content">
            <a href="{{ url($adminRegistration ? '/admin-registrations' : '/signup-option') }}" class="back-link">&larr; {{ $adminRegistration ? 'Back to registrations' : 'Back' }}</a>
            <h1>{{ $adminRegistration ? 'Add a Client' : 'Client Registration' }}</h1>
            <p>{{ $adminRegistration ? 'Register a client on their behalf. Their account will be approved and ready to use.' : 'Create your account to get started' }}</p>
            @if($errors->any())
                <div style="color:red;margin-bottom:15px;">
                    <ul style="margin:0;padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="clientRegisterForm" method="POST" action="{{ $adminRegistration ? route('admin.clients.store') : url('/client-register') }}">
                @csrf
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name *</label>
                        <input type="text" id="firstName" name="firstName" value="{{ old('firstName') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name *</label>
                        <input type="text" id="lastName" name="lastName" value="{{ old('lastName') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="alias">Display Name (Optional)</label>
                    <input type="text" id="alias" name="alias" value="{{ old('alias') }}" placeholder="Business name or preferred display name">
                    <small style="color: #666; font-size: 12px;">{{ $adminRegistration ? 'This name will be shown on the client’s job posts instead of their personal name.' : 'This name will be shown on your job posts instead of your personal name' }}</small>
                </div>

                <div class="form-group">
                    <label for="email">Email Address *</label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number *</label>
                    <input type="tel" id="phone" name="phone" value="{{ old('phone') }}" required>
                </div>

                <div class="form-group">
                    <label for="dob">Date of Birth *</label>
                    <input type="date" id="dob" name="dob" value="{{ old('dob') }}" @if($adminRegistration) max="{{ now()->toDateString() }}" @endif required>
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <input type="text" id="address" name="address" value="{{ old('address') }}" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" value="{{ old('city') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State *</label>
                        <input type="text" id="state" name="state" value="{{ old('state') }}" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="zipcode">Post Code *</label>
                        <input type="text" id="zipcode" name="zipcode" value="{{ old('zipcode') }}" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country *</label>
                        <input type="text" id="country" name="country" value="{{ old('country') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <div class="password-field">
                        <input type="password" id="password" name="password" autocomplete="new-password" minlength="6" required>
                        <button type="button" class="password-toggle" aria-label="Show password" aria-controls="password" data-target="password">
                            <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M9.9 4.2A10.6 10.6 0 0 1 12 4c6.5 0 10 8 10 8a18.4 18.4 0 0 1-3.3 4.7"></path>
                                <path d="M14.1 14.1A3 3 0 0 1 9.9 9.9"></path>
                                <path d="M3 3l18 18"></path>
                                <path d="M6.6 6.6A17.8 17.8 0 0 0 2 12s3.5 8 10 8a9.9 9.9 0 0 0 4.8-1.3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password *</label>
                    <div class="password-field">
                        <input type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" minlength="6" required>
                        <button type="button" class="password-toggle" aria-label="Show confirm password" aria-controls="password_confirmation" data-target="password_confirmation">
                            <svg class="eye-open" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7S2 12 2 12Z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg class="eye-closed" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M9.9 4.2A10.6 10.6 0 0 1 12 4c6.5 0 10 8 10 8a18.4 18.4 0 0 1-3.3 4.7"></path>
                                <path d="M14.1 14.1A3 3 0 0 1 9.9 9.9"></path>
                                <path d="M3 3l18 18"></path>
                                <path d="M6.6 6.6A17.8 17.8 0 0 0 2 12s3.5 8 10 8a9.9 9.9 0 0 0 4.8-1.3"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="form-group">
                    <label class="checkbox-label" for="privacy_rules_disclaimer">
                        <input type="checkbox" id="privacy_rules_disclaimer" name="privacy_rules_disclaimer" value="1" {{ old('privacy_rules_disclaimer') ? 'checked' : '' }} required>
                        <span>{{ $adminRegistration ? "Privacy Rules Disclaimer * - I acknowledge that the client information I provide will be handled according to Our Care's privacy rules and used to process this client registration." : "Privacy Rules Disclaimer * - I acknowledge that the information I provide will be handled according to Our Care's privacy rules and used to process my registration." }}</span>
                    </label>
                </div>

                @if($recaptchaSiteKey)
                    <div class="form-group recaptcha-group">
                        <div class="g-recaptcha" data-sitekey="{{ $recaptchaSiteKey }}"></div>
                    </div>
                @endif

                @error('g-recaptcha-response')
                    <div style="color:red;margin-bottom:15px;text-align:left;font-size:13px;">{{ $message }}</div>
                @enderror

                @error('privacy_rules_disclaimer')
                    <div style="color:red;margin-bottom:15px;text-align:left;font-size:13px;">{{ $message }}</div>
                @enderror

                @if($recaptchaSiteKey)
                    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
                @endif

                <script>
                    document.querySelectorAll('.password-toggle').forEach(function (button) {
                        button.addEventListener('click', function () {
                            var input = document.getElementById(button.dataset.target);
                            var isPassword = input.type === 'password';

                            input.type = isPassword ? 'text' : 'password';
                            button.classList.toggle('is-visible', isPassword);
                            button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                        });
                    });
                </script>

                <button type="submit" class="register-btn">{{ $adminRegistration ? 'Create Client Account' : 'Create Account' }}</button>
            </form>

            @unless($adminRegistration)
            <div class="login-link">
                Already have an account? <a href="{{ url('/login') }}">Sign In</a>
            </div>
            @endunless
        </div>
    </div>


</body>
</html>
