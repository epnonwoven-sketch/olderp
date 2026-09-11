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
        Schema::table('order_roll_bag_types', function (Blueprint $table) {
            $table->foreign(['bag_type_id'], 'order_roll_bag_types_bag_type_id_fkey')->references(['id'])->on('bag_type_masters')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['order_id'], 'order_roll_bag_types_order_id_fkey')->references(['id'])->on('order_punch_details')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_roll_bag_types', function (Blueprint $table) {
            $table->dropForeign('order_roll_bag_types_bag_type_id_fkey');
            $table->dropForeign('order_roll_bag_types_order_id_fkey');
        });
    }
};
