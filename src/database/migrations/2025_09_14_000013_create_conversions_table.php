<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversions', function (Blueprint $table) {
            $table->id()->comment('конвертация');
            $table->foreignId('boiler_capacity_id')->nullable()->constrained('boilers_capacity')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('responsible_user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('author_user_id')->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('note')->nullable();
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conversions');
    }
};


