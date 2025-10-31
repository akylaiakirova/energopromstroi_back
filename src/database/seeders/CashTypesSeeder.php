<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CashTypesSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            // isIncome = false (расходы)
            ['isIncome' => false, 'name' => 'Аренда авто'],
            ['isIncome' => false, 'name' => 'Аренда офиса'],
            ['isIncome' => false, 'name' => 'ГОИК'],
            ['isIncome' => false, 'name' => 'ГОПП'],
            ['isIncome' => false, 'name' => 'Заработная плата'],
            ['isIncome' => false, 'name' => 'Иные расходы'],
            ['isIncome' => false, 'name' => 'Ком. услуги'],
            ['isIncome' => false, 'name' => 'Командировочные'],
            ['isIncome' => false, 'name' => 'Налоги'],
            ['isIncome' => false, 'name' => 'Покупка материалов'],
            ['isIncome' => false, 'name' => 'Расходы офиса'],
            // isIncome = true (доходы)
            ['isIncome' => true, 'name' => 'Иные доходы'],
            ['isIncome' => true, 'name' => 'Продажа котлов'],
            ['isIncome' => true, 'name' => 'Установка отопительных систем'],
        ];

        foreach ($rows as $row) {
            DB::table('cash_types')->updateOrInsert(
                ['name' => $row['name']],
                ['isIncome' => $row['isIncome']]
            );
        }
    }
}


