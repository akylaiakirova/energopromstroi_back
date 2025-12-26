<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConversionMaterial extends Model
{
    use HasFactory;

    protected $table = 'conversion_materials';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updateAt';

    protected $guarded = [];

    protected $casts = [
        'countStandard' => 'decimal:3',
        'countFact' => 'decimal:3',
    ];

    public function conversion(): BelongsTo
    {
        return $this->belongsTo(Conversion::class, 'conversions_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}



