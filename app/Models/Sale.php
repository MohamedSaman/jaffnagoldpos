<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_no', 'customer_id', 'total_amount', 'discount',
        'grand_total', 'paid_amount', 'due_amount', 'payment_method', 'user_id',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'discount'     => 'decimal:2',
        'grand_total'  => 'decimal:2',
        'paid_amount'  => 'decimal:2',
        'due_amount'   => 'decimal:2',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(SalePayment::class);
    }

    public static function generateInvoiceNo(): string
    {
        $last = self::latest()->first();
        $number = $last ? ((int) substr($last->invoice_no, 4)) + 1 : 1;
        return 'INV-' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }
}
