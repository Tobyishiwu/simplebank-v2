<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * Updated fillable attributes for SB-00000002
     */
    protected $fillable = [
        'user_id',
        'account_id',
        'category',      // transfer, airtime, data, electricity
        'type',          // debit, credit
        'amount',
        'balance_after', // Fixed the "Field doesn't have default value" error
        'status',        // pending, successful, failed
        'reference',     // SB_XXXXXXXX
        'description',
        'service_id',     // mtn, airtel, dstv, etc.
        'variation_code', // Data plans or TV packages
        'token',          // For electricity purchases
        'response_payload' // Raw API response for auditing
    ];

    /**
     * Relationship to User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship to Account
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
