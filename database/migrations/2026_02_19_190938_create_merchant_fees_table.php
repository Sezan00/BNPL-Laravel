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
        Schema::create('merchant_fees', function (Blueprint $table) {
               $table->id();

        $table->foreignId('payment_id')
            ->constrained()
            ->onDelete('cascade');

        $table->foreignId('merchant_id')
            ->constrained()
            ->onDelete('cascade');

        $table->decimal('gross_amount', 12, 2);  
        $table->decimal('fee_percentage', 5, 2);  
        $table->decimal('fee_amount', 12, 2);     
        $table->decimal('net_amount', 12, 2);     

    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('merchant_fees');
    }
};
