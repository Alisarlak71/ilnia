<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // One row per payment attempt. An order with payments cannot be deleted.
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->restrictOnDelete();
            $table->string('gateway', 30);                          // e.g. zarinpal
            $table->unsignedBigInteger('amount');                   // Toman; must equal orders.total
            $table->string('authority', 100)->nullable()->unique(); // gateway's id for this attempt
            $table->string('ref_id', 100)->nullable();              // bank reference after verify
            $table->string('status', 20)->default('pending')->index();
            $table->json('payload')->nullable();                    // raw gateway responses (no secrets)
            $table->timestamp('verified_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
