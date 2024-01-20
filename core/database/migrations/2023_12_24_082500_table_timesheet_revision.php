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
        Schema::create('timesheet_revision', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('timesheet_id')->unsigned();
            $table->uuid('karyawan_id');

            $table->mediumText('remark_revision');

            // $table->unsignedBigInteger('status')->default(0);

            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at')->nullable();

            $table->foreign('timesheet_id')->references('id')->on('timesheet');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('timesheet_revision');
    }
};
