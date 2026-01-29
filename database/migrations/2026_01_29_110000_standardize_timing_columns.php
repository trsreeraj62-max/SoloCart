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
        // For Banners
        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'start_date')) {
                $table->renameColumn('start_date', 'start_at');
            }
            if (Schema::hasColumn('banners', 'end_date')) {
                $table->renameColumn('end_date', 'end_at');
            }
        });

        // For Categories
        Schema::table('categories', function (Blueprint $table) {
            if (Schema::hasColumn('categories', 'discount_start_date')) {
                $table->renameColumn('discount_start_date', 'start_at');
            }
            if (Schema::hasColumn('categories', 'discount_end_date')) {
                $table->renameColumn('discount_end_date', 'end_at');
            }
        });

        // For Products
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'discount_start_date')) {
                $table->renameColumn('discount_start_date', 'start_at');
            }
            if (Schema::hasColumn('products', 'discount_end_date')) {
                $table->renameColumn('discount_end_date', 'end_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->renameColumn('start_at', 'start_date');
            $table->renameColumn('end_at', 'end_date');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->renameColumn('start_at', 'discount_start_date');
            $table->renameColumn('end_at', 'discount_end_date');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('start_at', 'discount_start_date');
            $table->renameColumn('end_at', 'discount_end_date');
        });
    }
};
