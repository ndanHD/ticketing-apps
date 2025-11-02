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
            $table->id();
            $table->foreignId('user_id')->constrained('tbl_users')->onDelete('cascade');
            $table->foreignId('shift_id')->constrained('tbl_shifts')->onDelete('cascade');
            $table->date('date');
            $table->timestamps();
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
