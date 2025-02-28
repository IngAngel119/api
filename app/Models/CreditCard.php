<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditCard extends Model
{
    use HasFactory;

    protected $table = 'credit_cards';

    protected $fillable = [
        'client_id',
        'card_number',
        'expiration_date',
        'credit_limit',
        'created_by',
        'deleted_at',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function paymentCcs()
    {
        return $this->hasMany(PaymentCc::class);
    }
}
