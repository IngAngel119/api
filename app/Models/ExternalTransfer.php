<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalTransfer extends Model
{
    use HasFactory;

    protected $table = 'external_transfers';

    protected $fillable = [
        'account_id',
        'amount',
        'reason',
        'receptor_account',
        'receiving_bank',
        'movement_date',
        'created_by',
        'deleted_at',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
