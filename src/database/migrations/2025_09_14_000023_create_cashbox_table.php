<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbox', function (Blueprint $table) {
            $table->id()->comment('касса');
            $table->boolean('isIncome');
            $table->foreignId('cash_types_id')->constrained('cash_types')->cascadeOnUpdate()->cascadeOnDelete();
            $table->decimal('sum', 14, 2);
            $table->text('note')->nullable();
            $table->json('files')->nullable();
            $table->dateTime('dateTime');
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashbox');
    }
};


