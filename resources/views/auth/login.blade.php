<x-guest-layout>
    <div style="width: 100%; max-width: 400px; background: white; padding: 30px; border-radius: 24px; border: 1px solid #EAECF0;">

        <div style="text-align: center; margin-bottom: 30px;">
            <x-application-logo />
            <h2 style="font-size: 24px; font-weight: 900; color: #101828; margin: 15px 0 5px 0;">Welcome Back</h2>
            <p style="font-size: 14px; color: #667085; margin: 0;">Login to your SB-{{ date('Y') }} account</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; font-size: 11px; font-weight: 800; color: #667085; margin-bottom: 8px; text-transform: uppercase;">Email Address</label>
                <input type="email" name="email" value="{{ old('email') }}" required autofocus
                    style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #D0D5DD; font-size: 16px; box-sizing: border-box;">
                @error('email')
                    <p style="color: #B42318; font-size: 12px; margin-top: 5px; font-weight: 700;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 20px;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <label style="font-size: 11px; font-weight: 800; color: #667085; text-transform: uppercase;">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" style="font-size: 12px; color: #00338D; font-weight: 800; text-decoration: none;">Forgot?</a>
                    @endif
                </div>
                <input type="password" name="password" required
                    style="width: 100%; padding: 14px; border-radius: 12px; border: 1px solid #D0D5DD; font-size: 16px; box-sizing: border-box;">
            </div>

            <button type="submit" style="width: 100%; background: #00338D; color: white; padding: 16px; border-radius: 14px; border: none; font-size: 16px; font-weight: 800; cursor: pointer;">
                Sign In
            </button>
        </form>

        @if (Route::has('register'))
            <p style="text-align: center; margin-top: 25px; font-size: 14px; color: #667085;">
                New here? <a href="{{ route('register') }}" style="color: #00338D; font-weight: 800; text-decoration: none;">Register Account</a>
            </p>
        @endif
    </div>
</x-guest-layout>
