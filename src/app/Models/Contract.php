<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    use HasFactory;

    protected $table = 'contracts';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];
}


