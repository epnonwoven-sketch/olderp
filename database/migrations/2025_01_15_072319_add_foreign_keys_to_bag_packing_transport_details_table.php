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
        Schema::table('bag_packing_transport_details', function (Blueprint $table) {
            $table->foreign(['bag_packing_id'], 'bag_packing_transport_details_bag_packing_id_fkey')->references(['id'])->on('bag_packings')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['pack_transport_id'], 'bag_packing_transport_details_pack_transport_id_fkey')->references(['id'])->on('bag_packing_transports')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_packing_transport_details', function (Blueprint $table) {
            $table->dropForeign('bag_packing_transport_details_bag_packing_id_fkey');
            $table->dropForeign('bag_packing_transport_details_pack_transport_id_fkey');
        });
    }
};
