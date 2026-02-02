<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AccountController extends Controller
{
    /**
     * Set or Update the Transaction PIN
     * Standardized to use 'transaction_pin' column
     */
    public function updatePin(Request $request)
    {
        $request->validate([
            'pin' => ['required', 'digits:4', 'confirmed'],
        ]);

        $user = Auth::user();
        $account = $user->account;

        // Securely hash the PIN before saving
        $account->update([
            'transaction_pin' => Hash::make($request->pin)
        ]);

        return back()->with('success', 'Transaction PIN updated successfully!');
    }

    /**
     * Handle Transfer between SB-Accounts
     */
    public function executeTransfer(Request $request)
    {
        $request->validate([
            'recipient_account' => ['required', 'string', 'exists:accounts,account_number'],
            'amount' => ['required', 'numeric', 'min:100'],
            'description' => ['nullable', 'string', 'max:100'],
            'pin' => ['required', 'digits:4'],
        ]);

        $sender = Auth::user();
        $senderAccount = $sender->account;

        // PIN SECURITY CHECK (Corrected column name)
        if (!Hash::check($request->pin, $senderAccount->transaction_pin)) {
            return response()->json(['status' => 'error', 'message' => 'Incorrect Transaction PIN.'], 403);
        }

        $recipientAccount = Account::where('account_number', $request->recipient_account)->first();

        if ($senderAccount->balance < $request->amount) {
            return response()->json(['status' => 'error', 'message' => 'Insufficient balance.'], 400);
        }

        if ($senderAccount->id === $recipientAccount->id) {
            return response()->json(['status' => 'error', 'message' => 'You cannot transfer to yourself.'], 400);
        }

        return DB::transaction(function () use ($sender, $senderAccount, $recipientAccount, $request) {
            $senderNewBalance = $senderAccount->balance - $request->amount;
            $senderAccount->update(['balance' => $senderNewBalance]);

            $recipientNewBalance = $recipientAccount->balance + $request->amount;
            $recipientAccount->update(['balance' => $recipientNewBalance]);

            // Debit Sender
            $sender->transactions()->create([
                'user_id' => $sender->id,
                'account_id' => $senderAccount->id,
                'amount' => $request->amount,
                'type' => 'debit',
                'category' => 'transfer',
                'description' => "Transfer to {$request->recipient_account}",
                'reference' => 'TRF-' . strtoupper(bin2hex(random_bytes(10))),
                'balance_after' => $senderNewBalance,
                'status' => 'completed',
            ]);

            // Credit Recipient
            $recipientAccount->user->transactions()->create([
                'user_id' => $recipientAccount->user_id,
                'account_id' => $recipientAccount->id,
                'amount' => $request->amount,
                'type' => 'credit',
                'category' => 'transfer',
                'description' => "Transfer from {$sender->name}",
                'reference' => 'TRF-' . strtoupper(bin2hex(random_bytes(10))),
                'balance_after' => $recipientNewBalance,
                'status' => 'completed',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Transfer successful!']);
        });
    }

    /**
     * Handle ATM or Manual Withdrawal
     */
    public function withdraw(Request $request)
    {
        $request->validate([
            'amount' => ['required', 'numeric', 'min:100'],
            'pin'    => ['required', 'digits:4'],
        ]);

        $user = Auth::user();
        $account = $user->account;

        // PIN SECURITY CHECK (Corrected column name)
        if (!Hash::check($request->pin, $account->transaction_pin)) {
            return response()->json(['status' => 'error', 'message' => 'Incorrect Transaction PIN.'], 403);
        }

        if ($account->balance < $request->amount) {
            return response()->json(['status' => 'error', 'message' => 'Insufficient balance.'], 400);
        }

        return DB::transaction(function () use ($user, $account, $request) {
            $newBalance = $account->balance - $request->amount;
            $account->update(['balance' => $newBalance]);

            $user->transactions()->create([
                'user_id'       => $user->id,
                'account_id'    => $account->id,
                'amount'        => $request->amount,
                'type'          => 'debit',
                'category'      => 'withdrawal',
                'description'   => 'Cash Withdrawal',
                'reference'     => 'WTH-' . strtoupper(bin2hex(random_bytes(10))),
                'balance_after' => $newBalance,
                'status'        => 'completed',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Withdrawal successful!']);
        });
    }

    /**
     * Handle Manual Deposit/Funding
     */
    public function deposit(Request $request)
    {
        $request->validate(['amount' => ['required', 'numeric', 'min:100']]);

        $user = Auth::user();
        $account = $user->account;

        return DB::transaction(function () use ($user, $account, $request) {
            $newBalance = $account->balance + $request->amount;
            $account->update(['balance' => $newBalance]);

            $user->transactions()->create([
                'user_id'       => $user->id,
                'account_id'    => $account->id,
                'amount'        => $request->amount,
                'type'          => 'credit',
                'category'      => 'deposit',
                'description'   => 'Account Funding',
                'reference'     => 'DEP-' . strtoupper(bin2hex(random_bytes(10))),
                'balance_after' => $newBalance,
                'status'        => 'completed',
            ]);

            return response()->json(['status' => 'success', 'message' => 'Deposit successful!']);
        });
    }
}
