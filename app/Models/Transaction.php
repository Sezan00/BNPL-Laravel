<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'user_id',
        'type',
        'amount',
        'balance_after',
        'payment_id',
        'description',
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function merchant(){
        return $this->belongsTo(Merchant::class);
    }

    public function payment(){
        return $this->belongsTo(Payment::class);
    }
}
