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
        Schema::create('shift', function (Blueprint $table) {
            $table->id();
            $table->string('nama_shift');

            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at')->nullable();
        });

        Schema::create('jamkerja', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('shift_id');
            $table->string('hari');
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();

            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at')->nullable();

            $table->foreign('shift_id')->references('id')->on('shift');
        });

        Schema::table('timesheet', function (Blueprint $table) {
            $table->foreign('karyawan_id')->references('id')->on('karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shift');
        Schema::dropIfExists('jamkerja');
    }
};
