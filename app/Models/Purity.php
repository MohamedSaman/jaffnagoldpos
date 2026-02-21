<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purity extends Model
{
    protected $fillable = ['name', 'percentage'];

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function jewelleryRates()
    {
        return $this->hasMany(JewelleryRate::class);
    }

    public function latestRate()
    {
        return $this->hasOne(JewelleryRate::class)->latestOfMany('date');
    }
}
