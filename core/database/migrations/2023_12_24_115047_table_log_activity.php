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
        Schema::create('log_activity', function (Blueprint $table) {
            $table->id();
            $table->string('name_activity');
            $table->string('description');
            $table->string('target_id')->nullable();

            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_activity');
    }
};
