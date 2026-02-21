<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'opening_balance'];

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function totalDue(): float
    {
        return (float) $this->sales()->sum('due_amount');
    }

    public function totalPurchased(): float
    {
        return (float) $this->sales()->sum('grand_total');
    }
}
