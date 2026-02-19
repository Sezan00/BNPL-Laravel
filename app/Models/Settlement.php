<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settlement extends Model
{
    protected $table = 'settelements';
    protected $fillable = [
        'merchant_id',
        'gross_amount',
        'total_fee',
        'settled_amount',
        'currency',
        'status',
        'settled_status',
        'settled_at',
        'notes',
    ];

    public function merchant()
    {
        return $this->belongsTo(Merchant::class);
    }


    
public function payments()
{
    return $this->hasMany(Payment::class, 'receiver_id', 'merchant_id')
                ->where('receiver_type', 'merchant');
}

    
}
