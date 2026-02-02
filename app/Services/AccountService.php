<?php

namespace App\Services;

use App\Models\Account;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AccountService
{
    public const DAILY_WITHDRAWAL_LIMIT = 100000;

    public function deposit(Account $account, float $amount): void
    {
        DB::transaction(function () use ($account, $amount) {
            // Re-fetch with lock to ensure we have the absolute latest balance
            $lockedAccount = Account::where('id', $account->id)->lockForUpdate()->first();

            $lockedAccount->balance += $amount;
            $lockedAccount->save();

            $lockedAccount->transactions()->create([
                'type' => 'deposit',
                'amount' => $amount,
                'balance_after' => $lockedAccount->balance,
            ]);
        });
    }

    public function withdraw(Account $account, float $amount): void
    {
        DB::transaction(function () use ($account, $amount) {
            // Lock the account row immediately
            $lockedAccount = Account::where('id', $account->id)->lockForUpdate()->first();

            if ($amount > $lockedAccount->balance) {
                throw new \DomainException('Insufficient balance');
            }

            $this->assertDailyLimit($lockedAccount, $amount);

            $lockedAccount->balance -= $amount;
            $lockedAccount->save();

            $lockedAccount->transactions()->create([
                'type' => 'withdrawal',
                'amount' => $amount,
                'balance_after' => $lockedAccount->balance,
            ]);
        });
    }

    public function transfer(Account $from, Account $to, float $amount): void
    {
        if ($from->id === $to->id) {
            throw new \DomainException('Cannot transfer to the same account');
        }

        DB::transaction(function () use ($from, $to, $amount) {
            // Lock both accounts to prevent deadlocks (always lock in same order by ID)
            $ids = collect([$from->id, $to->id])->sort();
            $accounts = Account::whereIn('id', $ids)->lockForUpdate()->get();

            $lockedFrom = $accounts->firstWhere('id', $from->id);
            $lockedTo = $accounts->firstWhere('id', $to->id);

            if ($amount > $lockedFrom->balance) {
                throw new \DomainException('Insufficient balance');
            }

            $this->assertDailyLimit($lockedFrom, $amount);

            // Sender
            $lockedFrom->balance -= $amount;
            $lockedFrom->save();

            $lockedFrom->transactions()->create([
                'type' => 'transfer_out',
                'amount' => $amount,
                'balance_after' => $lockedFrom->balance,
            ]);

            // Receiver
            $lockedTo->balance += $amount;
            $lockedTo->save();

            $lockedTo->transactions()->create([
                'type' => 'transfer_in',
                'amount' => $amount,
                'balance_after' => $lockedTo->balance,
            ]);
        });
    }

    public function dailyStats(Account $account): array
    {
        $withdrawnToday = $this->withdrawnToday($account);

        return [
            'dailyLimit' => self::DAILY_WITHDRAWAL_LIMIT,
            'withdrawnToday' => $withdrawnToday,
            'remainingLimit' => max(self::DAILY_WITHDRAWAL_LIMIT - $withdrawnToday, 0),
        ];
    }

    private function withdrawnToday(Account $account): float
    {
        // Inside a transaction, this uses the locked account data
        return $account->transactions()
            ->whereIn('type', ['withdrawal', 'transfer_out'])
            ->whereDate('created_at', Carbon::today())
            ->sum('amount');
    }

    private function assertDailyLimit(Account $account, float $amount): void
    {
        if (($this->withdrawnToday($account) + $amount) > self::DAILY_WITHDRAWAL_LIMIT) {
            throw new \DomainException('Daily withdrawal limit exceeded');
        }
    }
}
