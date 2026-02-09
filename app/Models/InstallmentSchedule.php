<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstallmentSchedule extends Model
{
    use HasFactory;
    protected $table = 'installment_schedule';
     protected $fillable = [
        'installment_id',
        'installment_no',
        'amount',
        'due_date',
        'status',
        'paid_at',
    ];

    public function installment(){
    return $this->belongsTo(Installment::class);
    }
}
