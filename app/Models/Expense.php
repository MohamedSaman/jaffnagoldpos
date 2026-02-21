<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['title', 'amount', 'date', 'description'];

    protected $casts = [
        'date'   => 'date',
        'amount' => 'decimal:2',
    ];
}
