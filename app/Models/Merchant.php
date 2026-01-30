<?php

namespace App\Models;

use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Sanctum\HasApiTokens;

class Merchant extends Authenticatable
{
    use Billable;
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'merchant_name',
        'document_id',
        'document_number',
        'email',
        'password',
        'phone',
        'business_name',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function document(){
        return $this->belongsTo(Document::class);
    }
}
