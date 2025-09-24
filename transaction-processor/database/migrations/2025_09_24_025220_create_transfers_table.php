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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_code')->unique(); // TRF_xxx
            $table->unsignedBigInteger('amount'); // minor units
            $table->string('currency', 10)->default('GHS');
            $table->string('reference')->index(); // client reference
            $table->string('recipient'); // store recipient_code for simplicity
            $table->string('reason')->nullable();
            $table->string('source')->default('balance');
            $table->string('status')->default('pending'); // pending|success|failed|reversed
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
