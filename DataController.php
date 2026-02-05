<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class DataController extends Controller
{
    /**
     * STEP 1: Network Selection
     */
    public function index()
    {
        return view('services.data.index');
    }

    /**
     * STEP 2: Plan Selection
     */
    public function selectPlan(Request $request)
    {
        $provider = $request->provider ?? old('provider');
        $phone    = $request->phone ?? old('phone');

        if (!$provider || !$phone) {
            return redirect()
                ->route('services.data.index')
                ->withErrors(['provider' => 'Session expired. Please try again.']);
        }

        $plans = $this->fetchDataPlans($provider);

        if (empty($plans)) {
            return redirect()
                ->route('services.data.index')
                ->withErrors(['provider' => 'Unable to retrieve data plans. Try again shortly.']);
        }

        return view('services.data.select_plan', compact('plans', 'provider', 'phone'));
    }

    /**
     * STEP 3: Process Data Purchase
     */
    public function process(Request $request)
    {
        $request->validate([
            'provider' => 'required|in:mtn,airtel,glo,9mobile',
            'phone'    => 'required|digits:11',
            'plan'     => 'required|string',
            'pin'      => 'required|digits:4',
        ]);

        $user = auth()->user();

        /* 🔐 Verify Transaction PIN */
        if (!Hash::check($request->pin, $user->transaction_pin)) {
            return back()
                ->withErrors(['pin' => 'Incorrect transaction PIN'])
                ->withInput();
        }

        /* 🔎 Re-fetch plan securely */
        $plan = $this->getPlanByCode($request->provider, $request->plan);

        if (!$plan) {
            return back()
                ->withErrors(['plan' => 'Invalid or unavailable data plan'])
                ->withInput();
        }

        $amount = (float) $plan['variation_amount'];

        try {
            DB::transaction(function () use ($user, $amount, $request, $plan) {

                $account = $user->account()->lockForUpdate()->first();

                if (!$account) {
                    throw new \Exception('Wallet not found.');
                }

                if ($account->balance < $amount) {
                    throw new \Exception(
                        'Insufficient balance. Available ₦' . number_format($account->balance, 2)
                    );
                }

                /* 💸 Deduct wallet balance */
                $account->balance -= $amount;
                $account->save();

                /* 🧾 Log BILL PAYMENT transaction */
                Transaction::create([
                    'account_id'    => $account->id,
                    'type'          => 'withdrawal',

                    // 🔑 THESE FIX EVERYTHING
                    'service'       => 'data',
                    'title'         => 'DATA BUNDLE',
                    'reference'     => 'PP-' . strtoupper(Str::random(10)),
                    'status'        => 'success',

                    'amount'        => $amount,
                    'balance_after' => $account->balance,

                    'meta' => [
                        'phone' => $request->phone,
                        'plan'  => $plan['name'],
                        'size'  => $plan['name'],
                        'network' => strtoupper($request->provider),
                    ],
                ]);
            });

        } catch (\Exception $e) {
            return back()
                ->withErrors(['pin' => $e->getMessage()])
                ->withInput();
        }

        /* ✅ GLOBAL SUCCESS ALERT (NOW VISIBLE) */
        return redirect()
            ->route('dashboard')
            ->with('success', 'Data purchase successful.');
    }

    /**
     * Fetch all data plans for provider
     */
    private function fetchDataPlans(string $provider): array
    {
        $serviceID = strtolower($provider) . '-data';

        try {
            $response = Http::withHeaders([
                'api-key'    => config('services.vtpass.key'),
                'public-key' => config('services.vtpass.public'),
            ])->get(
                'https://sandbox.vtpass.com/api/service-variations',
                ['serviceID' => $serviceID]
            );

            if ($response->successful()) {
                return $response->json('content.varations', []);
            }
        } catch (\Throwable $e) {
            return [];
        }

        return [];
    }

    /**
     * Fetch single plan by variation code
     */
    private function getPlanByCode(string $provider, string $code): ?array
    {
        foreach ($this->fetchDataPlans($provider) as $plan) {
            if (($plan['variation_code'] ?? null) === $code) {
                return $plan;
            }
        }

        return null;
    }
}
