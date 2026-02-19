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
        'payment_type',
        'settled_status'
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function merchant()
    {
        return $this->belongsTo(Merchant::class, 'receiver_id');
    }

    public function scopeUnsettled($query)
    {
        return $query->where('receiver_type', 'merchant')
            ->where('settled_status', 0); 
    }
}
