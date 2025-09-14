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
}


