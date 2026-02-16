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
        Schema::create('settlement_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('merchant_id')
                ->constrained()
                ->onDelete('cascade');

            $table->string('account_holder_name');
            $table->string('bank_name');
            $table->string('bank_account_number');
            $table->string('bank_adress')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('ifsc_swift_code')->nullable();

            $table->enum('payout_method', ['bank', 'wallet', 'stripe'])->default('bank');
            $table->string('currency', 10)->default('USD');

            $table->enum('status', ['active', 'inactive'])->default('active');

            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settlement_accounts');
    }
};
