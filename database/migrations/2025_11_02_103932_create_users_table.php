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
        Schema::create('tbl_users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('role_id');
            $table->uuid('outlet_id')->nullable();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('job_tittle');
            $table->boolean('must_change_password')->default(true);
            $table->timestamp('last_change_password')->nullable();
            $table->string('reset_token')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('role_id')->references('id')->on('tbl_roles')->cascadeOnDelete();
            $table->foreign('outlet_id')->references('id')->on('tbl_outlets')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropForeign(['outlet_id']);
            $table->dropForeign(['deleted_by']);
        });
        Schema::dropIfExists('tbl_users');
    }
};
