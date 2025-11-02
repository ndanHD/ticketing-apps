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
            $table->id();
            $table->foreignId('ticket_type_id')->constrained('tbl_ticket_types')->onDelete('cascade');
            $table->foreignId('sla_id')->constrained('tbl_slas')->onDelete('cascade');
            $table->foreignId('assign_to')->nullable()->constrained('tbl_users')->onDelete('set null');
            $table->foreignId('created_by')->constrained('tbl_users')->onDelete('cascade');
            $table->enum('status', ['open', 'in_progress', 'closed'])->default('open');
            $table->text('detail')->nullable();
            $table->timestamps();
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
