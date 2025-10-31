<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('write_off', function (Blueprint $table) {
            $table->id()->comment('списание материалов');
            $table->foreignId('material_id')->constrained('materials')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('count');
            $table->decimal('price_for_1', 12, 2)->nullable();
            $table->string('note')->nullable();
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('write_off');
    }
};


