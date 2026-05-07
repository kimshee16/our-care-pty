<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - Choose Role</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <style>
        .sign-option-container {
            max-width: 600px;
            margin: 0 auto;
            padding: 40px 20px;
            text-align: center;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .sign-option-content {
            background: white;
            padding: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .sign-option-content h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }

        .sign-option-content p {
            color: #666;
            margin-bottom: 40px;
            font-size: 16px;
        }

        .options-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }

        .option-card {
            padding: 30px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .option-card:hover {
            border-color: #007bff;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.2);
            transform: translateY(-2px);
        }

        .option-card h2 {
            color: #333;
            font-size: 22px;
            margin-bottom: 15px;
        }

        .option-card p {
            color: #666;
            font-size: 14px;
            margin: 0;
        }

        .option-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        @media (max-width: 600px) {
            .options-grid {
                grid-template-columns: 1fr;
            }

            .sign-option-content {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="sign-option-container">
        <div class="sign-option-content">
            <h1>Create Account</h1>
            <p>Choose how you'd like to sign up</p>

            <div class="options-grid">
                <a href="{{ url('/client-register') }}" class="option-card">
                    <div class="option-icon">👤</div>
                    <h2>Client</h2>
                    <p>Looking for healthcare services</p>
                </a>

                <a href="{{ url('/healthcare-register') }}" class="option-card">
                    <div class="option-icon">👨‍⚕️</div>
                    <h2>Healthcare Worker</h2>
                    <p>Providing healthcare services</p>
                </a>
            </div>

            <a href="{{ url('/login') }}" class="back-link">← Back to Login</a>
        </div>
    </div>
</body>
</html>
