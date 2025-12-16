<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_types', function (Blueprint $table) {
            $table->id()->comment('типы расходов и доходов');
            $table->boolean('isIncome');
            $table->string('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_types');
    }
};


