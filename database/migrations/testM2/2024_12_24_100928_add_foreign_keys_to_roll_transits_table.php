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
        Schema::table('roll_transits', function (Blueprint $table) {
            $table->foreign(['bag_type_id'], 'roll_transits_bag_type_id_fkey')->references(['id'])->on('bag_type_masters')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['client_detail_id'], 'roll_transits_client_detail_id_fkey')->references(['id'])->on('client_detail_masters')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['cutting_machine_id'], 'roll_transits_cutting_machine_id_fkey')->references(['id'])->on('machine_maters')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['printing_machine_id'], 'roll_transits_printing_machine_id_fkey')->references(['id'])->on('machine_maters')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['vender_id'], 'roll_transits_vender_id_fkey')->references(['id'])->on('vendor_detail_masters')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roll_transits', function (Blueprint $table) {
            $table->dropForeign('roll_transits_bag_type_id_fkey');
            $table->dropForeign('roll_transits_client_detail_id_fkey');
            $table->dropForeign('roll_transits_cutting_machine_id_fkey');
            $table->dropForeign('roll_transits_printing_machine_id_fkey');
            $table->dropForeign('roll_transits_vender_id_fkey');
        });
    }
};
