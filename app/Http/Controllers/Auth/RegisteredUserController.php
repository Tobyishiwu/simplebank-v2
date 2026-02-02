<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        return DB::transaction(function () use ($request) {
            // 1. Create the User
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // 2. Generate Account Number (Format: SB-00000002)
            // Using str_pad ensures we hit your 8-digit requirement
            $accountNumber = 'SB-' . str_pad($user->id, 8, '0', STR_PAD_LEFT);

            // 3. Create the Bank Account with ₦1,000 Bonus
            $account = $user->account()->create([
                'account_number' => $accountNumber,
                'balance' => 1000.00,
                // If your migration added a 'pin' field, you might want to null it here or set a default
            ]);

            // 4. Record the Bonus in Transactions
            // We include 'balance_after' to satisfy your SQL constraint
            $user->transactions()->create([
                'account_id'    => $account->id,
                'amount'        => 1000.00,
                'type'          => 'credit',
                'category'      => 'bonus',
                'description'   => 'Welcome Bonus Credit',
                'reference'     => 'REG-' . strtoupper(bin2hex(random_bytes(4))),
                'balance_after' => 1000.00,
                'status'        => 'completed', // Good practice if you have a status column
            ]);

            event(new Registered($user));

            Auth::login($user);

            // Redirect to dashboard as requested
            return redirect()->route('dashboard')->with('success', 'Account created! ₦1,000 has been credited.');
        });
    }
}
