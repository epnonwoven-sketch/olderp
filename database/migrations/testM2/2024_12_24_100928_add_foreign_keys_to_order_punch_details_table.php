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
        Schema::table('order_punch_details', function (Blueprint $table) {
            $table->foreign(['client_detail_id'], 'order_punch_details_client_detail_id_fkey')->references(['id'])->on('client_detail_masters')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['payment_mode_id'], 'order_punch_details_payment_mode_id_fkey')->references(['id'])->on('payment_mode_masters')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_punch_details', function (Blueprint $table) {
            $table->dropForeign('order_punch_details_client_detail_id_fkey');
            $table->dropForeign('order_punch_details_payment_mode_id_fkey');
        });
    }
};
