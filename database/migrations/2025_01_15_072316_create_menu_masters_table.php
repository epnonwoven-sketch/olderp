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
        Schema::create('menu_masters', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('menu_name')->nullable();
            $table->integer('order_no')->nullable();
            $table->bigInteger('parent_menu_mstr_id');
            $table->string('url_path')->default('#');
            $table->text('query_string')->nullable();
            $table->string('menu_icon', 50)->nullable()->default('lni lni-grid-alt');
            $table->smallInteger('menu_type')->nullable();
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
        Schema::dropIfExists('menu_masters');
    }
};
