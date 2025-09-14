<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stocks_balance', function (Blueprint $table) {
            $table->id()->comment('остаток материалов');
            $table->foreignId('boiler_capacity_id')->constrained('boilers_capacity')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('count');
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stocks_balance');
    }
};


