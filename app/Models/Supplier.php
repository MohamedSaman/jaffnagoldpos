<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = ['name', 'phone', 'address', 'balance'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function totalDue(): float
    {
        return (float) $this->purchases()->sum('due_amount');
    }
}
