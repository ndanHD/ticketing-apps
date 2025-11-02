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
        Schema::create('tbl_ticket_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ticket_id')->constrained('tbl_tickets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('tbl_users')->onDelete('cascade');
            $table->text('comment');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_ticket_comments');
    }
};
