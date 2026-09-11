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
            $table->text('order_no')->nullable();
            $table->date('order_date')->default(DB::raw("now()"));
            $table->bigInteger('client_detail_id');
            $table->date('estimate_delivery_date');
            $table->date('delivery_date')->nullable();
            $table->bigInteger('bag_type_id')->nullable();
            $table->string('bag_quality', 50)->nullable();
            $table->decimal('bag_gsm', 18)->nullable();
            $table->json('bag_gsm_json')->nullable();
            $table->string('units', 50)->nullable();
            $table->decimal('total_units', 18)->nullable();
            $table->decimal('rate_per_unit', 18)->nullable();
            $table->decimal('bag_w', 18)->nullable();
            $table->decimal('bag_l', 18)->nullable();
            $table->decimal('bag_g', 18)->nullable();
            $table->text('bag_loop_color')->nullable();
            $table->json('bag_color')->nullable();
            $table->decimal('booked_units', 18)->nullable()->default(0);
            $table->decimal('disbursed_units', 18)->nullable()->default(0);
            $table->boolean('is_delivered')->default(false);
            $table->bigInteger('payment_mode_id')->nullable();
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
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
