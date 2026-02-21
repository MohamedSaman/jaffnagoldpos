<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = [
        'sale_id', 'product_id', 'weight', 'rate',
        'making_charge', 'wastage_amount', 'total',
    ];

    protected $casts = [
        'weight'         => 'decimal:3',
        'rate'           => 'decimal:2',
        'making_charge'  => 'decimal:2',
        'wastage_amount' => 'decimal:2',
        'total'          => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
