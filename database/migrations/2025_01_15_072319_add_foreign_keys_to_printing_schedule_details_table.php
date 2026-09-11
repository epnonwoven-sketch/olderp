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
        Schema::table('printing_schedule_details', function (Blueprint $table) {
            $table->foreign(['machine_id'], 'printing_schedule_details_machine_id_fkey')->references(['id'])->on('machine_maters')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('printing_schedule_details', function (Blueprint $table) {
            $table->dropForeign('printing_schedule_details_machine_id_fkey');
        });
    }
};
