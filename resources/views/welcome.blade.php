<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SimpleBank</title>
    <style>
        body { margin: 0; font-family: 'Inter', sans-serif; background: #00338D; height: 100vh; display: flex; align-items: center; justify-content: center; color: white; }
        .container { text-align: center; width: 90%; max-width: 400px; padding: 20px; }
        .logo { font-size: 40px; font-weight: 900; letter-spacing: -1px; margin-bottom: 10px; }
        .tagline { font-size: 16px; opacity: 0.8; margin-bottom: 40px; font-weight: 500; }
        .btn { display: block; padding: 16px; border-radius: 14px; text-decoration: none; font-weight: 800; font-size: 15px; margin-bottom: 12px; transition: 0.2s; }
        .btn-white { background: white; color: #00338D; }
        .btn-outline { border: 1px solid rgba(255,255,255,0.3); color: white; }
        .btn:active { transform: scale(0.98); }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">SimpleBank</div>
        <p class="tagline">Banking made simple for everyone.</p>

        @if (Route::has('login'))
            <div style="margin-top: 20px;">
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-white">Go to Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-white">Login to Account</a>

                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="btn btn-outline">Create New Account</a>
                    @endif
                @endauth
            </div>
        @endif

        <p style="font-size: 11px; opacity: 0.5; margin-top: 40px;">&copy; 2026 SimpleBank Inc.</p>
    </div>
</body>
</html>
