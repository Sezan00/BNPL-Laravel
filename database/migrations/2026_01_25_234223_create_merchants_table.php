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
             $table->foreignId('document_id')->nullable()->constrained('documents')->nullOnDelete();
             $table->string('document_number')->nullable();
             $table->string('merchant_name');
             $table->string('email')->unique();
             $table->string('phone', 20)->nullable();
             $table->string('password');
             $table->string('business_name')->nullable();
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
