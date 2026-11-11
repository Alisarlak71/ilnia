<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Snapshot of each line at order time. The variant link is kept for stock updates,
        // but names and prices are copied so the order never changes afterwards.
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('product_name');
            $table->string('variant_title', 100);
            $table->unsignedInteger('weight_grams');
            $table->unsignedBigInteger('unit_price');               // Toman
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('line_total');               // Toman
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
