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
        Schema::table('order_items', function (Blueprint $table) {
            $table->decimal('order_commission_rate', 10, 2);
            $table->decimal('order_commission', 10, 2);
            $table->decimal('vendor_earnings', 10, 2);
            $table->decimal('total_price', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'order_commission_rate',
                'order_commission',
                'vendor_earnings',
                'total_price',

                ]);
        });
    }
};
