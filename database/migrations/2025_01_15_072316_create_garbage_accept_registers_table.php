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
        Schema::create('garbage_accept_registers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('roll_id')->unique('garbage_accept_registers_roll_id_key');
            $table->decimal('total_qtr', 18)->default(0);
            $table->bigInteger('user_id')->nullable();
            $table->bigInteger('operator_id')->nullable();
            $table->bigInteger('helper_id')->nullable();
            $table->string('shift', 20);
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('garbage_accept_registers');
    }
};
