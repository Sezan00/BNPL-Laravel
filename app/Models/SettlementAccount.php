<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SettlementAccount extends Model
{
    protected $fillable = [
    'merchant_id',
    'account_holder_name',
    'bank_name',
    'bank_account_number',
    'bank_adress',
    'bank_branch',
    'ifsc_swift_code',
    'payout_method',
    'currency',
    'status',
    'notes',
];

}
