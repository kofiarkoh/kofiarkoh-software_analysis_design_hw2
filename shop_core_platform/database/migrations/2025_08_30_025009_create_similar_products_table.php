<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('similar_products', function (Blueprint $table) {
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->foreignId('similar_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->double('score');

            // Composite primary key (product_id, similar_id)
            $table->primary(['product_id', 'similar_id'], 'pk_similar');

            // Helpful secondary indexes (optional but good for joins/filters)
            $table->index('product_id', 'idx_product');
            $table->index('similar_id', 'idx_similar');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('similar_products');
    }
};
