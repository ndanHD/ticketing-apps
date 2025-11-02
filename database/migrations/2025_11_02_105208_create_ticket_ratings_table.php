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
        Schema::create('tbl_ticket_ratings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ticket_id');
            $table->uuid('user_id');
            $table->uuid('target_user_id');
            $table->tinyInteger('rating');
            $table->timestamps();

            $table->foreign('ticket_id')->references('id')->on('tbl_tickets')->cascadeOnDelete();
            $table->foreign('user_id')->references('id')->on('tbl_users')->cascadeOnDelete();
            $table->foreign('target_user_id')->references('id')->on('tbl_users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ticket_ratings');
    }
};
