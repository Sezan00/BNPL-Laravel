<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPackeg extends Model
{
    protected $table = 'installment_packeges';
     protected $fillable = [
        'name',
        'installment_count',
        'fixed_profit',
        'interest_percent',
        'min_amount',
        'is_active'
    ];

}
