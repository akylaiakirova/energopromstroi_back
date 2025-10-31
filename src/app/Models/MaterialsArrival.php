<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaterialsArrival extends Model
{
    use HasFactory;

    protected $table = 'materials_arrival';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    protected $casts = [
        'material_id' => 'integer',
        'count' => 'integer',
        'price_for_1' => 'decimal:2',
        'total_price' => 'decimal:2',
        'supplier_id' => 'integer',
    ];

    /** Relation to material */
    public function material()
    {
        return $this->belongsTo(Material::class, 'material_id');
    }

    /** Relation to supplier */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }
}


