<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'product_code', 'name', 'category_id', 'purity_id',
        'gross_weight', 'stone_weight', 'net_weight',
        'making_charge_type', 'making_charge', 'wastage_percentage',
        'stock_quantity', 'barcode', 'image', 'status',
    ];

    protected $casts = [
        'status' => 'boolean',
        'gross_weight' => 'decimal:3',
        'stone_weight' => 'decimal:3',
        'net_weight' => 'decimal:3',
        'making_charge' => 'decimal:2',
        'wastage_percentage' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function purity()
    {
        return $this->belongsTo(Purity::class);
    }

    public function saleItems()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    /**
     * Calculate price for this product given a rate per gram.
     */
    public function calculatePrice(float $ratePerGram, float $weight = null, float $discount = 0): array
    {
        $netWeight = $weight ?? (float) $this->net_weight;
        $goldValue = $netWeight * $ratePerGram;
        $wastageAmount = $goldValue * ((float) $this->wastage_percentage / 100);

        if ($this->making_charge_type === 'per_gram') {
            $makingChargeTotal = $netWeight * (float) $this->making_charge;
        } else {
            $makingChargeTotal = (float) $this->making_charge;
        }

        $total = $goldValue + $wastageAmount + $makingChargeTotal;
        $grandTotal = $total - $discount;

        return [
            'net_weight'          => $netWeight,
            'rate'                => $ratePerGram,
            'gold_value'          => round($goldValue, 2),
            'wastage_amount'      => round($wastageAmount, 2),
            'making_charge'       => round($makingChargeTotal, 2),
            'total'               => round($total, 2),
            'discount'            => round($discount, 2),
            'grand_total'         => round($grandTotal, 2),
        ];
    }
}
