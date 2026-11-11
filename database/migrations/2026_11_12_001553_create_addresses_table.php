<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Customer's saved shipping addresses. Orders copy (snapshot) the address, so
        // editing or deleting an address never changes a past order.
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('recipient_name', 100);
            $table->string('mobile', 11);             // 09xxxxxxxxx
            $table->string('province', 50);           // one of App\Support\IranProvinces
            $table->string('city', 100);
            $table->string('address_line', 500);
            $table->string('postal_code', 10);        // 10 English digits
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
