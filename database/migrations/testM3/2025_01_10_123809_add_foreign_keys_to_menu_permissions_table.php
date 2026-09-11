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
        Schema::table('menu_permissions', function (Blueprint $table) {
            $table->foreign(['menu_master_id'], 'menu_permissions_menu_master_id_fkeys')->references(['id'])->on('menu_masters')->onUpdate('no action')->onDelete('no action');
            $table->foreign(['user_type_master_id'], 'menu_permissions_user_type_master_id_fkeys')->references(['id'])->on('user_type_masters')->onUpdate('no action')->onDelete('no action');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_permissions', function (Blueprint $table) {
            $table->dropForeign('menu_permissions_menu_master_id_fkeys');
            $table->dropForeign('menu_permissions_user_type_master_id_fkeys');
        });
    }
};
