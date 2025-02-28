<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvestmentMovement extends Model
{
    use HasFactory;

    protected $table = 'investment_movements';

    protected $fillable = [
        'account_id',
        'amount',
        'inversion_type',
        'payment_date',
        'created_by',
        'deleted_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
