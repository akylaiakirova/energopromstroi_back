<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    use HasFactory;

    protected $table = 'invoice_products';

    public $timestamps = false;

    protected $guarded = [];
}


