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
        Schema::create('client_detail_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('client_name')->unique('client_detail_masters_client_name_key');
            $table->string('mobile_no', 100)->nullable();
            $table->string('email', 100)->nullable();
            $table->text('address')->nullable();
            $table->boolean('lock_status')->default(false);
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
            $table->softDeletes();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->smallInteger('sector_id')->nullable();
            $table->string('secondary_mobile_no', 100)->nullable();
            $table->string('temporary_mobile_no', 100)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_detail_masters');
    }
};
