<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentCc extends Model
{
    use HasFactory;

    protected $table = 'payment_ccs';

    protected $fillable = [
        'card_id',
        'minimum_payment_amount',
        'interest_free_amount',
        'total_amount',
        'cut_off_date',
        'payment_date',
        'movement_date',
        'created_by',
        'deleted_at',
    ];

    public function creditCard()
    {
        return $this->belongsTo(CreditCard::class);
    }
}
