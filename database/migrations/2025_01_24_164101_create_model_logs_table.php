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
        Schema::create('model_logs', function (Blueprint $table) {
            $table->id();
            $table->string('model_type'); // The model class name (e.g., App\Models\User)
            $table->unsignedBigInteger('model_id'); // The primary key of the model
            $table->string('action'); // Action type (e.g., created, updated, deleted)
            $table->json('changes')->nullable(); // JSON field for changes (old and new values)
            $table->string('route_name')->nullable(); // Route name, if applicable
            $table->json('payload')->nullable(); // Route name, if applicable
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('url')->nullable(); // Request URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('model_logs');
    }
};
