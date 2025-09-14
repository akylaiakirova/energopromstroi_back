<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplatePayment extends Model
{
    use HasFactory;

    protected $table = 'templates_payments';

    public const CREATED_AT = 'createAt';
    public const UPDATED_AT = 'updatedAt';

    protected $guarded = [];
}


