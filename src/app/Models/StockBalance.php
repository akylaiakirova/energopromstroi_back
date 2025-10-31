<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockBalance extends Model
{
    use HasFactory;

    protected $table = 'stocks_balance';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    /**
     * Связь с материалом.
     */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}


