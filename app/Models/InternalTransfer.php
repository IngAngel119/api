<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InternalTransfer extends Model
{
    use HasFactory;

    protected $table = 'internal_transfers';

    protected $fillable = [
        'account_id',
        'amount',
        'receptor_account',
        'movement_date',
        'created_by',
        'deleted_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
