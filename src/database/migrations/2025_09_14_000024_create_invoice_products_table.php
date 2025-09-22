<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_products', function (Blueprint $table) {
            $table->id()->comment('товары в накладной');
            $table->foreignId('boiler_capacity_id')->constrained('boilers_capacity')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('count');
            $table->decimal('price_for_1', 12, 2);
            $table->decimal('total_price', 14, 2);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_products');
    }
};


