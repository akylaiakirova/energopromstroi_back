<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversions', function (Blueprint $table) {
            $table->id()->comment('склад/конвертация - для index главное, но в conversion_materials детали этой таблицы');
            $table->foreignId('boiler_capacity_id')->nullable()->constrained('boilers_capacity')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('responsible_user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('createAt')->useCurrent()->comment('дата начала конвертации');
            $table->timestamp('finishAt')->nullable()->comment('дата завершении конвертации, тут мы получили готовый котел уже');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};