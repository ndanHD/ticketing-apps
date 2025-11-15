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
        Schema::create('tbl_notification_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('whatsapp')->default(true);
            $table->boolean('email')->default(true);
            $table->boolean('desktop')->default(true);
            $table->timestamps();
        });

        DB::table('tbl_notification_settings')->insert([
            'email' => true,
            'whatsapp' => true,
            'desktop' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_notification_settings');
    }
};
