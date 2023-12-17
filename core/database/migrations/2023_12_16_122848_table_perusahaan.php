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
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->char('identify_id', 16);
            $table->string('nama_perusahaan');
            $table->mediumText('address');
            $table->string('phone_number');
            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perusahaan');
    }
};
