<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Таблица материалов.
     * Комментарий на поле id: "материалы".
     */
    public function up(): void
    {
        Schema::create('materials', function (Blueprint $table) {
            $table->id()->comment('база/материалы');
            $table->string('name');
            $table->string('unit');
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('materials');
    }
};


