<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'supplier_id', 'invoice_no', 'total_amount',
        'paid_amount', 'due_amount', 'date',
    ];

    protected $casts = [
        'date'         => 'date',
        'total_amount' => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'due_amount'   => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public static function generateInvoiceNo(): string
    {
        $last = self::latest()->first();
        $number = $last ? ((int) substr($last->invoice_no, 4)) + 1 : 1;
        return 'PUR-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
