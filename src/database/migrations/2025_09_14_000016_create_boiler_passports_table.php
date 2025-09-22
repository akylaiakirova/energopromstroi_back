<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('boiler_passports', function (Blueprint $table) {
            $table->id()->comment('паспорта котлов');
            $table->foreignId('boiler_capacity_id')->constrained('boilers_capacity')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('number');
            $table->json('files');
            $table->text('note')->nullable();
            $table->dateTime('date');
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('boiler_passports');
    }
};


