<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvrAcceptance extends Model
{
    use HasFactory;

    protected $table = 'avr_acceptance';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];
}


