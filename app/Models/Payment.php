<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'stripe_id',
        'sender_id', 
        'receiver_type', 
        'receiver_id',
        'amount',
        'status',
        'payment_type'        
    ];
}
