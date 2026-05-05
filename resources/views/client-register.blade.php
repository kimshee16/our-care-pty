<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Sign Up</title>
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
            <a href="{{ url('/signup-option') }}" class="back-link">← Back</a>
            <h1>Client Registration</h1>
            <p>Create your account to get started</p>
            @if($errors->any())
                <div style="color:red;margin-bottom:15px;">
                    <ul style="margin:0;padding-left:20px;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form id="clientRegisterForm" method="POST" action="{{ url('/client-register') }}">
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
                    <small style="color: #666; font-size: 12px;">This name will be shown on your job posts instead of your personal name</small>
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
                    <input type="date" id="dob" name="dob" required>
                </div>

                <div class="form-group">
                    <label for="address">Address *</label>
                    <input type="text" id="address" name="address" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State *</label>
                        <input type="text" id="state" name="state" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="zipcode">Zip Code *</label>
                        <input type="text" id="zipcode" name="zipcode" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country *</label>
                        <input type="text" id="country" name="country" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="password">Password *</label>
                    <input type="password" id="password" name="password" required>
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirm Password *</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required>
                </div>

                <button type="submit" class="register-btn">Create Account</button>
            </form>

            <div class="login-link">
                Already have an account? <a href="{{ url('/login') }}">Sign In</a>
            </div>
        </div>
    </div>


</body>
</html>
