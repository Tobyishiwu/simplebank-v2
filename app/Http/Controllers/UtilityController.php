<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\VTPassService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UtilityController extends Controller
{
    protected $vtPass;

    public function __construct(VTPassService $vtPass)
    {
        $this->vtPass = $vtPass;
    }

    public function pay(Request $request)
    {
        $user = Auth::user();
        $account = $user->account;

        // 1. Validation for all utility types
        $request->validate([
            'service_id' => 'required|string',
            'amount' => 'required|numeric|min:100',
            'pin' => 'required|string|size:4',
            'billers_code' => 'required|string', // Used for Phone or Meter No
            'variation_code' => 'nullable|string' // Required for Data
        ]);

        // 2. PIN Verification (SB-00000002 Security Standard)
        if (!Hash::check($request->pin, $account->transaction_pin)) {
            return response()->json(['status' => 'error', 'message' => 'Incorrect Transaction PIN'], 403);
        }

        // 3. Balance Check
        if ($account->balance < $request->amount) {
            return response()->json(['status' => 'error', 'message' => 'Insufficient funds in account'], 400);
        }

        // 4. Generate VTpass Request ID (YYYYMMDDHHIISS + Unique String)
        $requestId = now()->format('YmdHi') . Str::random(5);
        $reference = 'SB-UTIL-' . strtoupper(Str::random(10));

        // 5. Execute Purchase via Service
        $result = $this->vtPass->purchase([
            'request_id' => $requestId,
            'service_id' => $request->service_id,
            'amount' => $request->amount,
            'billers_code' => $request->billers_code,
            'variation_code' => $request->variation_code,
        ]);

        // 6. Handle Response
        if (isset($result['code']) && $result['code'] === '000') {

            $newBalance = $account->balance - $request->amount;

            // Log Transaction
            $tx = Transaction::create([
                'user_id' => $user->id,
                'account_id' => $account->id,
                'category' => $this->getCategory($request->service_id),
                'type' => 'debit',
                'amount' => $request->amount,
                'balance_after' => $newBalance,
                'status' => 'successful',
                'reference' => $reference,
                'description' => strtoupper($request->service_id) . " payment: " . $request->billers_code,
                'token' => $result['purchased_code'] ?? ($result['mainToken'] ?? null), // For Electricity
            ]);

            // Update Account Balance
            $account->update(['balance' => $newBalance]);

            return response()->json([
                'status' => 'success',
                'message' => 'Transaction Successful',
                'data' => [
                    'amount' => "₦" . number_format($request->amount, 2),
                    'reference' => $reference,
                    'date' => now()->format('d M Y, h:i A'),
                    'token' => $tx->token,
                    'description' => $tx->description
                ]
            ]);
        }

        // Error Response from Provider
        return response()->json([
            'status' => 'error',
            'message' => $result['response_description'] ?? 'Provider connection failed'
        ], 400);
    }

    /**
     * Helper to categorize transactions for the dashboard UI
     */
    private function getCategory($serviceId)
    {
        if (str_contains($serviceId, 'data')) return 'data';
        if (str_contains($serviceId, 'electric')) return 'electricity';
        if (str_contains($serviceId, 'dstv') || str_contains($serviceId, 'gotv')) return 'cable';
        return 'airtime';
    }
}
