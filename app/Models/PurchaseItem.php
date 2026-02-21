<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = [
        'purchase_id', 'product_id', 'weight', 'cost_per_gram', 'total',
    ];

    protected $casts = [
        'weight'       => 'decimal:3',
        'cost_per_gram' => 'decimal:2',
        'total'        => 'decimal:2',
    ];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
