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
        Schema::table('roll_quality_grade_maps', function (Blueprint $table) {
            $table->foreign(['grade_id'], 'roll_quality_grade_maps_grade_id_fkey')->references(['id'])->on('grade_masters')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['roll_quality_id'], 'roll_quality_grade_maps_roll_quality_id_fkey')->references(['id'])->on('roll_qality_masters')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roll_quality_grade_maps', function (Blueprint $table) {
            $table->dropForeign('roll_quality_grade_maps_grade_id_fkey');
            $table->dropForeign('roll_quality_grade_maps_roll_quality_id_fkey');
        });
    }
};
