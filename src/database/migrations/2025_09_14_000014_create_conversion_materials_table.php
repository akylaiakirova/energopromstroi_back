<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Конвертация материалов в рамках события conversions.
     * Комментарий на поле id: "конвертация материалов".
     */
    public function up(): void
    {
        Schema::create('conversion_materials', function (Blueprint $table) {
            $table->id()->comment('конвертация материалов');
            $table->foreignId('conversions_id')->constrained('conversions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('countStandard');
            $table->unsignedInteger('countFact');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversion_materials');
    }
};


