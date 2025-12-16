<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MaterialsConsumption extends Model
{
    use HasFactory;

    protected $table = 'materials_consumption';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    public function boilerCapacity(): BelongsTo
    {
        return $this->belongsTo(BoilerCapacity::class, 'boiler_capacity_id');
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class, 'material_id');
    }
}

