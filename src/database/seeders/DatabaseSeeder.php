<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Гарантируем наличие роли admin
        $roleId = DB::table('roles')->where('name', 'admin')->value('id');
        if (! $roleId) {
            $roleId = DB::table('roles')->insertGetId(['name' => 'admin']);
        }

        // Создаём единственного пользователя
        DB::table('users')->updateOrInsert(
            ['email' => 'akylaiakirova@gmail.com'],
            [
                'role_id' => $roleId,
                'position' => 'Администратор',
                'name' => 'Акылай',
                'surname' => 'Акирова',
                'phone' => '+70000000000',
                'password' => Hash::make('qweqwe123'),
                'has_access' => true,
                'createAt' => now(),
                'updatedAt' => now(),
            ]
        );

        // Тестовый администратор
        DB::table('users')->updateOrInsert(
            ['email' => 'test@bukefal.com'],
            [
                'role_id' => $roleId,
                'position' => 'Администратор',
                'name' => 'Тариел',
                'surname' => 'Жолдошов',
                'phone' => '+996507303311',
                'password' => Hash::make('qweqwe123'),
                'has_access' => true,
                'createAt' => now(),
                'updatedAt' => now(),
            ]
        );

        // Типы доходов/расходов
        $this->call(CashTypesSeeder::class);
    }
}
