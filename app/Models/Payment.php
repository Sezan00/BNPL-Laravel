<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'sender_id', 
        'receiver_type', 
        'receiver_id',
        'amount',
        'status',        
    ];
}
