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
        Schema::create('bag_packings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('packing_no', 50)->nullable()->unique('bag_packings_packing_no_key');
            $table->decimal('packing_weight', 18);
            $table->integer('packing_bag_pieces')->nullable();
            $table->date('packing_date')->nullable()->default(DB::raw("now()"));
            $table->integer('packing_status')->default(1)->comment('1 = in factory, 2 = in godown, 3 = in transport (factory to godown), 4 = dispatched for delivery');
            $table->bigInteger('order_id');
            $table->bigInteger('user_id')->nullable();
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
        Schema::dropIfExists('bag_packings');
    }
};
