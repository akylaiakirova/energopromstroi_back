<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateDocument extends Model
{
    use HasFactory;

    protected $table = 'templates_document';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];

    protected $casts = [
        'files' => 'array',
    ];
}


