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
        Schema::create('roll_quality_grade_maps', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('roll_quality_id')->nullable();
            $table->bigInteger('grade_id')->nullable();
            $table->bigInteger('user_id')->nullable();
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
        Schema::dropIfExists('roll_quality_grade_maps');
    }
};
