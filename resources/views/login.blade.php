<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Carehub — Login</title>
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <main class="login-main">
        <div class="card">
            <h1>Sign in to Carehub</h1>
            @if(session('status'))
                <div style="color:green;font-size:14px;margin-bottom:10px;">
                    {{ session('status') }}
                </div>
            @endif
            <form id="login-form" method="POST" action="{{ url('/login') }}">
                @csrf
                <label for="email">Email</label>
                <input id="email" name="email" type="email" placeholder="Enter your email" value="{{ old('email') }}">

                <label for="password">Password</label>
                <input id="password" name="password" type="password" placeholder="Enter password">

                @if($errors->any())
                    <div class="error" style="color:red;font-size:13px; margin-top:8px;">
                        {{ $errors->first() }}
                    </div>
                @endif

                <div class="form-actions">
                    <button type="submit" class="btn">Login</button>
                    <a class="back" href="{{ url('/') }}">Back to home</a>
                </div>
            </form>
        </div>
    </main>

</body>
</html>