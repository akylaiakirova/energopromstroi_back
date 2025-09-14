<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Устройства пользователей и их токены.
 */
class Device extends Model
{
    use HasFactory;

    protected $table = 'devices';

    public const CREATED_AT = 'createdAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];
}


