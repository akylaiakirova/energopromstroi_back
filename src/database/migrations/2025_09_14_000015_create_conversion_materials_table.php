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
            $table->id()->comment('склад/конвертация/конвертация материалов в дополнение к таблице conversions');
            $table->foreignId('conversions_id')->constrained('conversions')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained('materials')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('countStandard')->comment('нормативный расход материалов');
            $table->unsignedInteger('countFact')->comment('фактический расход материалов');
            $table->timestamp('createAt')->useCurrent()->comment('дата и время когда выдали мастеру данный материал');
            $table->timestamp('updateAt')->nullable()->comment('запись была отредактирована в это время');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversion_materials');
    }
};

// {
//     "boiler_capacity_id": 1,
//     "responsible_user_id": 1,
//     "note": "конвертация 1",
//     "conversion_materials": [
//         {   
//             "conversions_id": 1,
//             "material_id": 1,
//             "countStandard": 100,
//             "countFact": 100,
//             "createAt": "2025-01-01 12:00:00",
//             "updateAt": "2025-01-01 12:00:00"
//         },
//         {
//             "conversions_id": 1,
//             "material_id": 2,
//             "countStandard": 200,
//             "countFact": 200,
//             "createAt": "2025-01-01 12:00:00",
//             "updateAt": "2025-01-01 12:00:00"
//         }
//     ]
// }
