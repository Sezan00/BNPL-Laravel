<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ledger extends Model
{
     protected $fillable = [
        'user_id',
        'payment_id',
        'type',
        'amount',
        'balance_after',
        'description'
    ];
}
