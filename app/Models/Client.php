<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'created_by'];

    protected $dates = ['deleted_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class); 
    }

    public function creditCards()
    {
        return $this->hasMany(CreditCard::class);
    }
}
