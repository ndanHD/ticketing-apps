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
        Schema::table('tbl_tickets', function (Blueprint $table) {
            $table->text('pending_reason')->nullable()->after('status');
            $table->date('pending_until')->nullable()->after('pending_reason');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_tickets', function (Blueprint $table) {
            $table->dropColumn(['pending_reason', 'pending_until']);
        });
    }
};
