<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchantFee extends Model
{
    protected $table = 'merchant_fees';

    protected $fillable = [
        'payment_id',
        'merchant_id',
        'gross_amount',
        'fee_percentage',
        'fee_amount',
        'net_amount',
    ];
}
