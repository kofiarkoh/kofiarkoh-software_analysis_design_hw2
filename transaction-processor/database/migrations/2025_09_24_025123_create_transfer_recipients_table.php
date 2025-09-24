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
        Schema::create('transfer_recipients', function (Blueprint $table) {
            $table->id();
            $table->string('recipient_code')->unique(); // e.g., RCP_xxx
            $table->string('type'); // nuban, mobile_money, etc.
            $table->string('name');
            $table->string('account_number');
            $table->string('bank_code');
            $table->string('currency', 10)->default('GHS');
            $table->boolean('active')->default(true);
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfer_recipients');
    }
};
