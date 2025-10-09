<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Изменение таблицы users (сотрудники): добавляем бизнес-поля и связь с ролями.
     * Комментарий на поле id уже задан в базовой миграции Laravel.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Внешний ключ на роли (roles)
            $table->foreignId('role_id')
                ->after('id')
                ->constrained('roles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()->comment('1 админ, 2 офис-админ, 3 офис, 4 сварщик');

            // Должность
            $table->string('position')->nullable(false)->default('')->comment('Должность')->after('role_id');

            // Имя и фамилия (Laravel уже имеет name; добавим surname)
            $table->string('surname')->nullable()->comment('Фамилия')->after('name');

            // Контакты
            $table->string('phone')->nullable()->after('email');
            $table->string('whatsapp')->nullable()->after('phone');
            $table->string('telegram')->nullable()->after('whatsapp');

            // Паспортные данные
            $table->string('passport_number')->nullable()->after('telegram');
            $table->string('passport_pin')->nullable()->after('passport_number');

            // Зарплата
            $table->decimal('salary', 12, 2)->nullable()->after('passport_pin');

            // Прочее
            $table->text('comment')->nullable()->after('salary');
            $table->date('date_start')->nullable()->after('comment');
            $table->date('date_end')->nullable()->after('date_start');
            $table->boolean('has_access')->nullable()->after('date_end');

            // Переименуем timestamps по ТЗ (createAt/updatedAt). Создадим дублирующие поля.
            $table->timestamp('createAt')->nullable()->useCurrent()->after('has_access');
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate()->after('createAt');
        });
    }

    /**
     * Откат изменений.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropConstrainedForeignId('role_id');
            $table->dropColumn([
                'position',
                'surname',
                'phone',
                'whatsapp',
                'telegram',
                'passport_number',
                'passport_pin',
                'salary',
                'comment',
                'date_start',
                'date_end',
                'has_access',
                'createAt',
                'updatedAt',
            ]);
        });
    }
};


