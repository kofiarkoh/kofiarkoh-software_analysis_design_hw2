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
        Schema::table('orders', function (Blueprint $table) {
            if (Schema::hasColumn('orders', 'address_id')) {
                $table->dropForeign(['address_id']);
                $table->dropColumn('address_id');
            }

            // Add new fields
            $table->text('delivery_instructions')->nullable()->after('status');
            $table->string('nearby_city')->nullable()->after('delivery_instructions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Revert added columns
            $table->dropColumn(['delivery_instructions', 'nearby_city']);

            // Optionally re-add address_id if needed
            $table->unsignedBigInteger('address_id')->nullable()->after('user_id');
            $table->foreign('address_id')->references('id')->on('customer_addresses')->onDelete('set null');
        });
    }
};
