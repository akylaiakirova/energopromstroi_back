<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avr_acceptance', function (Blueprint $table) {
            $table->id()->comment('АВР и АКТ приемки');
            $table->dateTime('date');
            $table->json('files');
            $table->text('note')->nullable();
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avr_acceptance');
    }
};


