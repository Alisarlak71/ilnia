<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');                                         // e.g. «پست»
            $table->unsignedBigInteger('cost');                             // Toman
            // Orders weighing this much or more (>=) ship free. Null = never free.
            $table->unsignedInteger('free_over_weight_grams')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipping_methods');
    }
};
