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
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'discount_type')) {
                $table->enum('discount_type', ['percentage', 'flat'])->default('percentage')->after('price');
            }
            if (!Schema::hasColumn('products', 'discount_value')) {
                $table->decimal('discount_value', 10, 2)->default(0)->after('discount_type');
            }
            // Ensure start/end dates are timestamps and named correctly if we want to follow user request exactly, 
            // but we'll stick to existing column names if they exist.
        });

        Schema::table('banners', function (Blueprint $table) {
            if (!Schema::hasColumn('banners', 'start_date')) {
                $table->timestamp('start_date')->nullable();
            }
            if (!Schema::hasColumn('banners', 'end_date')) {
                $table->timestamp('end_date')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['discount_type', 'discount_value']);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date']);
        });
    }
};
