<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Создание таблицы ролей пользователей.
     * Комментарий на поле id: "роли".
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            // Первичный ключ с комментарием на русском языке
            $table->id()->comment('роли');

            $table->string('name')->unique();

            // Метки времени создания/обновления записи
            $table->timestamps();
        });

        // Первичные данные по ролям согласно ТЗ
        DB::table('roles')->insert([
            ['name' => 'admin',       'created_at' => now(), 'updated_at' => now()],
            ['name' => 'officeAdmin', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'office',      'created_at' => now(), 'updated_at' => now()],
            ['name' => 'welder',      'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Откат миграции.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};


