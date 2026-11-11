<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number', 20)->unique();                 // shown to the customer
            // Keep the order even if the customer account is deleted.
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status', 30)->default('pending_payment')->index();

            // Money (Toman, integer). total = subtotal + shipping_cost - discount
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('shipping_cost');
            $table->unsignedBigInteger('discount')->default(0);
            $table->unsignedBigInteger('total');
            $table->unsignedInteger('total_weight_grams');

            // Snapshot at the moment of ordering (later edits must not change past orders).
            $table->string('shipping_method_name');
            $table->string('recipient_name', 100);
            $table->string('recipient_mobile', 11);
            $table->string('province', 50);
            $table->string('city', 100);
            $table->string('address_line', 500);
            $table->string('postal_code', 10);

            $table->string('tracking_code', 100)->nullable();       // postal tracking code
            $table->text('customer_note')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
