<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoilerPassport extends Model
{
    use HasFactory;

    protected $table = 'boiler_passports';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    protected $casts = [
        'boiler_capacity_id' => 'integer',
        'files' => 'array',
        'date' => 'datetime',
    ];
}


