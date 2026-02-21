<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JewelleryRate extends Model
{
    protected $fillable = ['purity_id', 'rate_per_gram', 'date'];

    protected $casts = [
        'date' => 'date',
        'rate_per_gram' => 'decimal:2',
    ];

    public function purity()
    {
        return $this->belongsTo(Purity::class);
    }
}
