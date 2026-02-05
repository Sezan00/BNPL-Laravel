<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InstallmentPackeg extends Model
{
    protected $table = 'installment_packeges';
     protected $fillable = [
        'name',
        'term',
        'installment_count',
        'interest_percent',
        'fixed_profit',
        'is_active'
    ];

}
