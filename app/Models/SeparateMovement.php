<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeparateMovement extends Model
{
    use HasFactory;

    protected $fillable = ['account_id', 'amount', 'separate_name', 'payment_date', 'created_by'];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}

