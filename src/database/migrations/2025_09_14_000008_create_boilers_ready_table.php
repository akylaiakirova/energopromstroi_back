<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boilers_ready', function (Blueprint $table) {
            $table->id()->comment('готовые котлы');
            $table->foreignId('boiler_capacity_id')->nullable()->constrained('boilers_capacity')->cascadeOnUpdate()->cascadeOnDelete();
            $table->unsignedInteger('count')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnUpdate()->cascadeOnDelete();
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boilers_ready');
    }
};


