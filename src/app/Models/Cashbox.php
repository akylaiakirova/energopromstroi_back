<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cashbox extends Model
{
    use HasFactory;

    protected $table = 'cashbox';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    protected $casts = [
        'isIncome' => 'boolean',
        'files' => 'array',
        'sum' => 'decimal:2',
        'dateTime' => 'datetime',
        'cash_types_id' => 'integer',
    ];

    /**
     * Связь с типом операции кассы.
     */
    public function cashType()
    {
        return $this->belongsTo(CashType::class, 'cash_types_id');
    }
}


