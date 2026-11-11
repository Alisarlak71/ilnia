<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // «شرایط نگهداری» on the product page; differs per product type (honey, oil, kashk…).
            $table->text('storage_instructions')->nullable()->after('story');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('storage_instructions');
        });
    }
};
