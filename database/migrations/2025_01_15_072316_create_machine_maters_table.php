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
        Schema::create('machine_maters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name', 100)->unique('machine_maters_name_key');
            $table->boolean('is_cutting')->default(false);
            $table->boolean('is_printing')->default(false);
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
        Schema::dropIfExists('machine_maters');
    }
};
