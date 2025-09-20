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
        Schema::table('transactions', function (Blueprint $table) {

            // Drop existing foreign key first
            $table->dropForeign(['user_id']);
//            // Make the column nullable
            $table->foreignId('user_id')->nullable()->change();
//            // Re-add the foreign key
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('payment_url')->nullable()->change();


            $table->unsignedBigInteger('transactable_id');
            $table->string('transactable_type');


            $table->json('paystack_initial_response')->nullable();
            $table->json('paystack_final_response')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Drop modified foreign key
            $table->dropForeign(['user_id']);
            // Make column NOT NULL again
            $table->foreignId('user_id')->nullable(false)->change();
//            // Re-add original constraint
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->string('payment_url')->nullable(false)->change();
            $table->dropColumn(['transactable_id', 'transactable_type', 'paystack_initial_response', 'paystack_final_response']);
        });
    }
};
