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
        Schema::create('order_roll_bag_types', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('order_id');
            $table->bigInteger('bag_type_id');
            $table->bigInteger('roll_id')->nullable();
            $table->string('bag_unit', 50);
            $table->decimal('w', 18, 4)->nullable();
            $table->decimal('l', 18, 4)->nullable();
            $table->decimal('g', 18, 4)->nullable();
            $table->json('printing_color')->nullable();
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
        Schema::dropIfExists('order_roll_bag_types');
    }
};
