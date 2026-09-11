<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('order_punch_details', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('client_detail_id');
            $table->date('estimate_delivery_date');
            $table->date('delivery_date')->nullable();
            $table->boolean('is_delivered')->default(false);
            $table->bigInteger('payment_mode_id');
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->text('order_no')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_punch_details');
    }
};
