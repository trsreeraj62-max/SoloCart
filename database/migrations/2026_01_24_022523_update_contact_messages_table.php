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
        Schema::table('contact_messages', function (Blueprint $table) {
            if (!Schema::hasColumn('contact_messages', 'subject')) {
                $table->string('subject')->after('email');
            }
            if (!Schema::hasColumn('contact_messages', 'admin_reply')) {
                $table->text('admin_reply')->nullable()->after('message');
            }
            if (!Schema::hasColumn('contact_messages', 'status')) {
                $table->string('status')->default('new')->after('admin_reply');
            }
            if (Schema::hasColumn('contact_messages', 'reply')) {
                $table->dropColumn('reply');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contact_messages', function (Blueprint $table) {
            $table->dropColumn(['subject', 'admin_reply', 'status']);
            $table->text('reply')->nullable();
        });
    }
};
