<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AirtimeService;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AirtimeController extends Controller
{
    protected $airtimeService;

    public function __construct(AirtimeService $airtimeService)
    {
        $this->airtimeService = $airtimeService;
    }

    /**
     * Process the Airtime Purchase.
     */
    public function store(Request $request)
    {
        $request->validate([
            'network' => 'required|in:MTN,Airtel,Glo,9mobile',
            'phone'   => 'required|digits:11',
            'amount'  => 'required|numeric|min:100',
        ]);

        $user = Auth::user();
        $account = $user->account;

        // Check if SB-00000002 has enough funds
        if ($account->balance < $request->amount) {
            return back()->with('error', 'Insufficient SimpleBank balance.');
        }

        try {
            return DB::transaction(function () use ($request, $account) {
                // 1. Deduct locally from account balance
                $account->decrement('balance', $request->amount);
                $account->refresh();

                // 2. API Call to VTPass via AirtimeService
                $result = $this->airtimeService->purchase(
                    $request->network,
                    $request->phone,
                    $request->amount
                );

                // Check for VTPass success code '000'
                if (isset($result['code']) && $result['code'] === '000') {

                    // 3. Save Transaction as a 'debit' for the Dashboard history
                    Transaction::create([
                        'account_id'    => $account->id,
                        'type'          => 'debit', // Ensures red color/minus sign
                        'category'      => 'airtime',
                        'amount'        => $request->amount,
                        'balance_after' => $account->balance,
                        'description'   => "Airtime: {$request->network} to {$request->phone}",
                        'reference'     => $result['requestId'] ?? 'SB-AIR-'.time(),
                        'status'        => 'completed'
                    ]);

                    // Redirect to dashboard to trigger the Blue Toast notification
                    return redirect()->route('dashboard')->with('success', 'Airtime purchased successfully!');
                }

                // If API fails, we throw an exception to roll back the balance deduction
                throw new \Exception($result['response_description'] ?? 'Transaction Failed at Provider');
            });

        } catch (\Exception $e) {
            Log::error("Airtime Error for Account {$account->account_number}: " . $e->getMessage());

            // Return to form with error message in the Red Toast
            return redirect()->route('airtime.index')->with('error', 'Transaction Failed: ' . $e->getMessage());
        }
    }
}
