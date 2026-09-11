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
        Schema::create('roll_transits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('roll_no', 50)->nullable()->unique('roll_transits_roll_no_key');
            $table->date('purchase_date')->nullable();
            $table->bigInteger('vender_id');
            $table->smallInteger('gsm')->nullable();
            $table->json('gsm_json')->nullable();
            $table->decimal('gsm_variation', 18, 4)->nullable();
            $table->string('roll_color', 100)->nullable();
            $table->integer('length')->nullable();
            $table->decimal('size', 18, 1)->nullable();
            $table->decimal('net_weight', 18)->nullable();
            $table->decimal('gross_weight', 18)->nullable();
            $table->string('hardness', 10)->nullable();
            $table->string('roll_type', 10);
            $table->bigInteger('client_detail_id')->nullable();
            $table->date('estimate_delivery_date')->nullable();
            $table->date('delivery_date')->nullable();
            $table->boolean('is_delivered')->default(false);
            $table->bigInteger('bag_type_id')->nullable();
            $table->string('bag_unit', 50)->nullable();
            $table->decimal('w', 18, 1)->nullable();
            $table->decimal('l', 18, 1)->nullable();
            $table->decimal('g', 18, 1)->nullable();
            $table->json('printing_color')->nullable();
            $table->boolean('is_printed')->default(false);
            $table->date('printing_date')->nullable();
            $table->decimal('weight_after_print', 18)->nullable();
            $table->bigInteger('printing_machine_id')->nullable();
            $table->boolean('is_cut')->default(false);
            $table->date('cutting_date')->nullable();
            $table->decimal('weight_after_cutting', 18)->nullable();
            $table->bigInteger('cutting_machine_id')->nullable();
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->default(DB::raw("now()"));
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->string('loop_color', 100)->nullable();
            $table->bigInteger('quality_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roll_transits');
    }
};
