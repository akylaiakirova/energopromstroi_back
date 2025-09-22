<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConversionMaterial extends Model
{
    use HasFactory;

    protected $table = 'conversion_materials';

    public $timestamps = false;

    protected $guarded = [];
}


