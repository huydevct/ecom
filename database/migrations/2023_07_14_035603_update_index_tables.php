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
        Schema::table('carts', function (Blueprint $table) {
            $table->index(['product_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->index(['payment_date', 'payment_method']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carts', function (Blueprint $table) {
            $table->dropIndex(['product_id']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_date', 'payment_method']);
        });
    }
};
