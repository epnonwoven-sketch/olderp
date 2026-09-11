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
        Schema::table('roll_qality_masters', function (Blueprint $table) {
            $table->foreign(['vendor_id'], 'roll_qality_masters_vendor_id_fkey')->references(['id'])->on('vendor_detail_masters')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roll_qality_masters', function (Blueprint $table) {
            $table->dropForeign('roll_qality_masters_vendor_id_fkey');
        });
    }
};
