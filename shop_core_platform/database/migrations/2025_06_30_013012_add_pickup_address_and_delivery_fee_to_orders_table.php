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


            $table->decimal('cart_price', 10, 2);
            $table->decimal('delivery_fee', 10, 2);
            $table->unsignedBigInteger('delivery_city_id')->nullable();
            $table->foreign('delivery_city_id')
                ->references('id')
                ->on('delivery_cities')
                ->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['delivery_city_id']);
            $table->dropColumn('delivery_city_id');
            $table->dropColumn('delivery_fee');
            $table->dropColumn('cart_price');
        });
    }
};
