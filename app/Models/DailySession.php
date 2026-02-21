<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailySession extends Model
{
    protected $fillable = [
        'user_id', 'opening_balance', 'closing_balance', 'status', 'opened_at', 'closed_at'
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
