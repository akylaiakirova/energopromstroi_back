<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clients', function (Blueprint $table) {
            $table->id()->comment('клиенты');
            $table->string('number')->nullable();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->string('whatsapp')->nullable();
            $table->string('telegram')->nullable();
            $table->text('note')->nullable();
            $table->string('bank')->nullable();
            $table->string('bank_bik')->nullable();
            $table->string('bank_account')->nullable();
            $table->string('address_legal')->nullable();
            $table->string('address_fact')->nullable();
            $table->timestamp('createAt')->useCurrent();
            $table->timestamp('updatedAt')->nullable()->useCurrentOnUpdate();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};


