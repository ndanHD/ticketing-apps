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
        Schema::create('tbl_handler_shift_schedules', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('user_id');
            $table->uuid('shift_id');
            $table->date('date');
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('tbl_users')->cascadeOnDelete();
            $table->foreign('shift_id')->references('id')->on('tbl_shifts')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_handler_shift_schedul');
    }
};
