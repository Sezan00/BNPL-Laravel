<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('merchants', function (Blueprint $table) {
             $table->id();
             $table->string('merchant_name');
             $table->string('email')->unique();
             $table->string('phone', 20)->nullable();
             $table->string('password');
             $table->string('business_name')->nullable();
             $table->string('trade_license')->nullable();
             $table->boolean('is_active')->default(false);
             $table->enum('status', ['pending', 'approved', 'suspended', 'blocked'])->default('pending');

             $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchants');
    }
};
