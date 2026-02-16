<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'installment_schedule_id',
        'stripe_id',
        'sender_id', 
        'receiver_type', 
        'receiver_id',
        'amount',
        'status',
        'payment_type'        
    ];
    public function user() {
    return $this->belongsTo(User::class, 'sender_id');
}

}
