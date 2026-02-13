<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Installment extends Model
{
    use HasFactory;
     protected $table = 'installments';
    protected $fillable = [
        'user_id',
        'merchant_id',
        'payment_id',
        'package_id',
        'principal_amount',
        'paid_amount',
        'interest_amount',
        'total_payable',
        'remaining_balance',
        'status',
    ];

    public function merchant(){
        return $this->belongsTo(Merchant::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function schedules(){
        return $this->hasMany(InstallmentSchedule::class, 'installment_id');
    }
}
