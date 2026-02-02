<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable; // Removed HasApiTokens to fix the FatalError

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'is_admin' => 'boolean',
    ];

    /**
     * Link User to their Bank Account (SB-00000000)
     */
    public function account()
    {
        return $this->hasOne(Account::class);
    }

    /**
     * Link User to their Transactions
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
}
