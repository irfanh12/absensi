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
        Schema::create('presensi', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jamkerja_id');
            $table->uuid('karyawan_id');

            $table->string('status');

            $table->text('photo');
            $table->text('map_direction');

            $table->char('time', 5);

            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at')->nullable();

            $table->foreign('jamkerja_id')->references('id')->on('jamkerja');
            $table->foreign('karyawan_id')->references('id')->on('karyawan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi');
    }
};
