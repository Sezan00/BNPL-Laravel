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
        Schema::create('installment_packeges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('installment_count');
            $table->decimal('interest_percent', 5, 2)->default(0); 
            $table->decimal('fixed_profit', 10,2)->default(0);
            $table->decimal('min_amount', 12, 2);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('installment_packeges');
    }
};
