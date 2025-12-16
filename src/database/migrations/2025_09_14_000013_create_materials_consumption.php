<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('materials_consumption', function (Blueprint $table) {
            $table->id()->comment('склад/расход материалов - просто норматив, сколько материалов обычно затрачивается на какой либо котел');
            $table->foreignId('boiler_capacity_id')->constrained('boilers_capacity')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('countStandard');
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials_consumption');
    }
};


// 'materials_consumption': [
//     {
//         "boiler_capacity_id": 1,
//         "material_id": 1,
//         "countStandard": 100,
//         "createAt": "2025-01-01 12:00:00",
//         "updatedAt": "2025-01-01 12:00:00"
//     },
//     {
//         "boiler_capacity_id": 1,
//         "material_id": 2,
//         "countStandard": 200,
//         "createAt": "2025-01-01 12:00:00",
//         "updatedAt": "2025-01-01 12:00:00"
//     }
// ]