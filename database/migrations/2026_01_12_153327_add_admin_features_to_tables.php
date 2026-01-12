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
            $table->text('specifications')->nullable();
            $table->timestamp('discount_start_date')->nullable();
            $table->timestamp('discount_end_date')->nullable();
        });

        Schema::table('banners', function (Blueprint $table) {
            if (Schema::hasColumn('banners', 'link')) {
                $table->dropColumn('link');
            }
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->nullable();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('status')->default('active'); // active, suspended
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['specifications', 'discount_start_date', 'discount_end_date']);
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->string('link')->nullable();
            $table->dropColumn(['start_date', 'end_date']);
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
