<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification Pending - Our Care</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html, body {
            height: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            padding: 40px;
            max-width: 500px;
            width: 100%;
            text-align: center;
        }

        .icon {
            font-size: 60px;
            margin-bottom: 20px;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px);
            }
            50% {
                transform: translateY(-20px);
            }
        }

        h1 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #666;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .email-display {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #667eea;
        }

        .email-display p {
            color: #333;
            font-weight: 500;
            margin: 5px 0;
        }

        .email-text {
            color: #667eea;
            font-size: 18px;
            word-break: break-all;
        }

        .message {
            background: #e3f2fd;
            border: 1px solid #90caf9;
            color: #1976d2;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            text-align: left;
        }

        .message ul {
            list-style: none;
            margin-top: 10px;
        }

        .message li {
            padding: 8px 0;
            padding-left: 25px;
            position: relative;
        }

        .message li:before {
            content: "✓";
            position: absolute;
            left: 0;
            color: #4caf50;
            font-weight: bold;
        }

        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            flex-direction: column;
        }

        .btn {
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background: #5568d3;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-secondary {
            background: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
        }

        .btn-secondary:hover {
            background: #e8e8e8;
        }

        .contact-info {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #999;
            font-size: 14px;
        }

        .contact-info a {
            color: #667eea;
            text-decoration: none;
        }

        .contact-info a:hover {
            text-decoration: underline;
        }

        .status-success {
            background: #e8f5e9;
            border: 1px solid #c8e6c9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="icon">✉️</div>
            <h1>Email Verification Needed</h1>
            <p class="subtitle">Please verify your email address to continue</p>

            @if(session('status'))
            <div class="status-success">
                {{ session('status') }}
            </div>
            @endif

            @if(session('email'))
            <div class="email-display">
                <p>We sent a verification link to:</p>
                <p class="email-text">{{ session('email') }}</p>
            </div>
            @endif

            <div class="message">
                <strong>What you need to do:</strong>
                <ul>
                    <li>Open the email we sent you</li>
                    <li>Click the verification link in the email</li>
                    <li>Your account will be verified immediately</li>
                    <li>Return here to log in</li>
                </ul>
            </div>

            <div class="button-group">
                <a href="/login" class="btn btn-primary">Back to Login</a>
                <a href="/" class="btn btn-secondary">Go to Home</a>
            </div>

            <div class="contact-info">
                <p>Didn't receive the email?</p>
                <p>Check your spam folder or <a href="/">contact our support team</a></p>
            </div>
        </div>
    </div>
</body>
</html>
