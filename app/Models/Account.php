<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'account_number', 'balance', 'created_by', 'deleted_at'];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function movements()
    {
        return $this->hasMany(SeparateMovement::class, 'account_id');
    }
}
