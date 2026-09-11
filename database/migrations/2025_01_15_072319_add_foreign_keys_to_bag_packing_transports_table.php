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
        Schema::table('bag_packing_transports', function (Blueprint $table) {
            $table->foreign(['auto_id'], 'fk_auto')->references(['id'])->on('auto_details')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['transporter_id'], 'fk_transporter')->references(['id'])->on('transporter_details')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bag_packing_transports', function (Blueprint $table) {
            $table->dropForeign('fk_auto');
            $table->dropForeign('fk_transporter');
        });
    }
};
