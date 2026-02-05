<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Services\AirtimeService;
use Illuminate\Support\Facades\{Auth, DB, Log, Hash};

class AirtimeController extends Controller
{
    protected $airtimeService;

    public function __construct(AirtimeService $airtimeService)
    {
        $this->airtimeService = $airtimeService;
    }

    public function index()
    {
        return view('services.airtime', ['title' => 'Buy Airtime']);
    }

    public function process(Request $request)
    {
        // Fix: Convert provider to lowercase BEFORE validation to prevent "Invalid Provider" error
        if ($request->has('provider')) {
            $request->merge(['provider' => strtolower($request->provider)]);
        }

        // 1. Strict Validation (Matched to lowercase keys from Blade)
        $request->validate([
            'phone'    => 'required|digits:11',
            'amount'   => 'required|numeric|min:100',
            'pin'      => 'required|string|size:4',
            'provider' => 'required|in:mtn,airtel,glo,9mobile'
        ]);

        $user = Auth::user();
        $account = $user->account;

        // 2. PIN Presence Check
        if (!$user->transaction_pin) {
            return back()->withErrors(['pin' => 'Transaction PIN not set. Please visit Security settings.']);
        }

        // 3. Security Check
        if (!Hash::check($request->pin, $user->transaction_pin)) {
            return back()->withErrors(['pin' => 'Incorrect Transaction PIN.']);
        }

        // 4. Wallet Check (PayPoint Format SB-00000002)
        if (!$account || $account->balance < $request->amount) {
            $accNo = $account->account_number ?? 'SB-00000002';
            return back()->withErrors(['amount' => "Insufficient balance in your $accNo wallet."]);
        }

        // 5. Execution
        try {
            return DB::transaction(function () use ($request, $account, $user) {
                // Standardize network name for API (e.g., VTPass usually likes uppercase)
                $network = strtoupper($request->provider);
                $phone = $request->phone;
                $amount = (int) $request->amount;

                // API Call to AirtimeService
                $result = $this->airtimeService->purchase($network, $phone, $amount);

                if (isset($result['code']) && $result['code'] === '000') {

                    // Deduct Balance
                    $account->decrement('balance', $amount);
                    $newBalance = $account->fresh()->balance;

                    // Record Transaction
                    Transaction::create([
                        'user_id'       => $user->id,
                        'account_id'    => $account->id,
                        'type'          => 'debit',
                        'category'      => 'airtime',
                        'title'         => "$network AIRTIME",
                        'amount'        => $amount,
                        'balance_after' => $newBalance,
                        'description'   => "Recharge for $phone",
                        'reference'     => $result['requestId'] ?? 'PP-' . strtoupper(uniqid()),
                        'status'        => 'completed'
                    ]);

                    // REDIRECT with success data for the Receipt Modal in Dashboard
                    return redirect()->route('dashboard')->with('airtime_success', [
                        'amount'  => $amount,
                        'phone'   => $phone,
                        'network' => $network,
                        'ref'     => $result['requestId'] ?? 'PP-' . strtoupper(uniqid())
                    ]);
                }

                throw new \Exception($result['response_description'] ?? 'The airtime provider returned an error.');
            });
        } catch (\Exception $e) {
            Log::error("PayPoint Airtime Error: " . $e->getMessage());
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }
}
