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
        Schema::create('roll_data', function (Blueprint $table) {
            $table->bigInteger('id')->nullable();
            $table->text('roll_no')->nullable();
            $table->decimal('size', 18, 1)->nullable();
            $table->string('vendor_name', 100)->nullable();
            $table->string('client_name')->nullable();
            $table->string('bag_type', 100)->nullable();
            $table->text('purchase_date')->nullable();
            $table->text('estimate_delivery_date')->nullable();
            $table->text('delivery_date')->nullable();
            $table->text('printing_date')->nullable();
            $table->text('schedule_date_for_cutting')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roll_data');
    }
};
