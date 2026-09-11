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
        Schema::create('bag_packing_transports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('vehicle_no', 100)->nullable();
            $table->text('transporter_name')->nullable();
            $table->date('transport_date');
            $table->string('bill_no', 100)->nullable();
            $table->string('invoice_no', 100)->nullable();
            $table->integer('transport_status')->comment('3 = in transport (factory to godown), 4 = dispatched for delivery');
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('reiving_user_id')->nullable();
            $table->date('reiving_date')->nullable();
            $table->boolean('is_fully_reviewed')->default(false);
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->bigInteger('transporter_id')->nullable();
            $table->bigInteger('auto_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_packing_transports');
    }
};
