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
            $table->uuid('outlet_id')->nullable();
            $table->string('title');
            $table->text('description');
            $table->enum('status', ['open', 'in_progress', 'pending', 'resolved', 'closed', 'resolve'])->default('open');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('low');
            $table->timestamp('resolved_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->uuid('deleted_by')->nullable();

            $table->foreign('ticket_type_id')->references('id')->on('tbl_ticket_types')->cascadeOnDelete();
            $table->foreign('sla_id')->references('id')->on('tbl_slas')->cascadeOnDelete();
            $table->foreign('created_by')->references('id')->on('tbl_users')->cascadeOnDelete();
            $table->foreign('assign_to')->references('id')->on('tbl_users')->nullOnDelete();
            $table->foreign('outlet_id')->references('id')->on('tbl_outlets')->nullOnDelete();
            $table->foreign('deleted_by')->references('id')->on('tbl_users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tbl_tickets', function (Blueprint $table) {
            $table->dropForeign(['ticket_type_id']);
            $table->dropForeign(['sla_id']);
            $table->dropForeign(['assign_to']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['outlet_id']);
            $table->dropForeign(['deleted_by']);
        });
        Schema::dropIfExists('tbl_tickets');
    }
};
