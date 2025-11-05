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
        Schema::create('tbl_tickets', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('ticket_type_id');
            $table->uuid('sla_id');
            $table->uuid('created_by');
            $table->uuid('assign_to')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'resolved', 'closed'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high'])->default('low');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->foreign('ticket_type_id')->references('id')->on('tbl_ticket_types')->cascadeOnDelete();
            $table->foreign('sla_id')->references('id')->on('tbl_slas')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('tbl_users')->cascadeOnDelete();
            $table->foreign('assign_to')->references('id')->on('tbl_users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_tickets');
    }
};