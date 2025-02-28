<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentService extends Model
{
    use HasFactory;

    protected $table = 'payment_services';

    protected $fillable = [
        'account_id',
        'amount',
        'service_category',
        'destination_company',
        'is_domiciled',
        'payment_date',
        'created_by',
        'deleted_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
