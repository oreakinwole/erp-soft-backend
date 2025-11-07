<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payroll extends Model
{
    protected $fillable = [
        'staff_id',
        'period',
        'gross_pay',
        'deductions',
        'bonus',
        'net_pay',
        'status',
        'processed_at',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'gross_pay' => 'decimal:2',
        'deductions' => 'decimal:2',
        'bonus' => 'decimal:2',
        'net_pay' => 'decimal:2',
        'processed_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class);
    }
}
