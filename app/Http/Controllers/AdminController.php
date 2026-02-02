<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Transaction;

class AdminController extends Controller
{
    public function users()
    {
        return view('admin.users', [
            'users' => User::latest()->get(),
        ]);
    }

    public function accounts()
    {
        return view('admin.accounts', [
            'accounts' => Account::with('user')->latest()->get(),
        ]);
    }

    public function transactions()
    {
        return view('admin.transactions', [
            'transactions' => Transaction::with('account.user')
                ->latest()
                ->limit(500)
                ->get(),
        ]);
    }
}
