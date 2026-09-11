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
        Schema::create('bag_type_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('bag_type', 100)->unique('bag_type_masters_bag_type_key');
            $table->text('gsm_variation')->nullable();
            $table->text('roll_find')->nullable()->comment(' to find the roll from bag dtl when in pice');
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->text('roll_find_as_weight')->nullable()->comment(' to find the roll from bag dtl when in kg');
            $table->text('roll_size_find')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bag_type_masters');
    }
};
