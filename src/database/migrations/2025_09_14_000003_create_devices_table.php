<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Устройства пользователей, где храним информацию о девайсах и токенах.
     * Комментарий на поле id: "устройства пользователей".
     *
     * Профессиональный подход при множестве устройств:
     * - 1 пользователь может иметь N устройств (веб, мобильные, десктоп),
     * - для каждого устройства храним собственные access/refresh токены и мета-данные,
     * - это позволяет независимо завершать сессии на отдельных устройствах,
     *   задавать срок жизни и анализировать активность.
     */
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            // PK
            $table->id()->comment('устройства пользователей');

            // Связь на пользователя
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            // Информация об устройстве
            $table->string('device_name')->nullable();
            $table->string('device_model')->nullable();
            $table->string('device_os')->nullable();
            $table->string('fcm_token')->nullable();

            // Refresh-токен (храним только хеш), срок действия, флаги
            $table->text('refresh_token_hash')->nullable();
            $table->dateTime('refresh_token_expires_at')->nullable();
            $table->boolean('revoked')->default(false);

            // Сеансовые и системные метаданные
            $table->dateTime('last_session_date_time')->nullable(false);
            $table->string('last_ip')->nullable();
            $table->text('user_agent')->nullable();

            // Явные timestamps под требования ТЗ
            $table->timestamp('createdAt')->nullable(false)->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();

            // Индексы для ускорения выборок по пользователю и активным токенам
            $table->index(['user_id']);
        });
    }

    /**
     * Откат.
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};


