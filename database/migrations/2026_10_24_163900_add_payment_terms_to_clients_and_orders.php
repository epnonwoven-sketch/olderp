<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('client_detail_masters', function (Blueprint $table) {
            $table->string('payment_term', 20)->nullable();
            $table->integer('credit_days')->nullable();
            $table->text('payment_term_note')->nullable();
        });

        Schema::table('order_punch_details', function (Blueprint $table) {
            $table->string('payment_term', 20)->nullable();
            $table->integer('credit_days')->nullable();
            $table->text('payment_term_note')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('client_detail_masters', function (Blueprint $table) {
            $table->dropColumn(['payment_term', 'credit_days', 'payment_term_note']);
        });

        Schema::table('order_punch_details', function (Blueprint $table) {
            $table->dropColumn(['payment_term', 'credit_days', 'payment_term_note']);
        });
    }
};
