<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_embeddings', function (Blueprint $table) {
            // PK also FK → products.id
            $table->foreignId('product_id')
                ->constrained('products')
                ->cascadeOnDelete();

            $table->unsignedBigInteger('shop_id')->nullable()->index();

            $table->string('model', 100)->index();
            $table->json('vector');

            // Only an updated_at column (to match your Python model)
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            // Make product_id the PRIMARY KEY
            $table->primary('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_embeddings');
    }
};
