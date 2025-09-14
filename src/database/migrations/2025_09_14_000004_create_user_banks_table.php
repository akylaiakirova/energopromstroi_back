<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Реквизиты сотрудников.
     * Комментарий на поле id: "реквизиты сотрудников".
     */
    public function up(): void
    {
        Schema::create('user_banks', function (Blueprint $table) {
            // PK
            $table->id()->comment('реквизиты сотрудников');

            // FK на users
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Обязательные поля по ТЗ
            $table->string('bank_name');
            $table->string('bank_account_number');

            // Необязательные
            $table->string('bank_bik')->nullable();
            $table->string('address_registered')->nullable();
            $table->string('address_actual')->nullable();

            // Явные timestamps под требования ТЗ
            $table->timestamp('createAt')->nullable(false)->useCurrent();
            $table->timestamp('updatedAt')->nullable(false)->useCurrent()->useCurrentOnUpdate();

            $table->index(['user_id']);
        });
    }

    /**
     * Откат.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_banks');
    }
};


