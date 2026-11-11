<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per weight. Price and stock live here, not on the product.
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('title');                          // e.g. «۱ کیلوگرم»
            $table->unsignedInteger('weight_grams');          // used for shipping (free from 4000 g)
            // Toman, integer. Null = price not set yet by the admin → not purchasable.
            $table->unsignedBigInteger('price')->nullable();
            $table->unsignedInteger('stock')->default(0);
            $table->string('sku')->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
