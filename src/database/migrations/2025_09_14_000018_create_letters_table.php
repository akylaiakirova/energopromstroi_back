<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('letters', function (Blueprint $table) {
            $table->id()->comment('письма');
            $table->string('address');
            $table->string('theme');
            $table->text('text');
            $table->json('files')->nullable();
            $table->text('note')->nullable();
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('letters');
    }
};


