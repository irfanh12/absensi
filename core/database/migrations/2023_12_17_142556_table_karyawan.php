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
        Schema::create('karyawan', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('perusahaan_id');
            $table->char('identify_id', 16)->unique();
            $table->bigInteger('type_id')->default(1);
            $table->string('position');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number')->nullable();
            $table->date('birthdate')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->text('address')->nullable();
            $table->decimal('salary', 10, 2)->default(0.00);
            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at')->nullable();
            // $table->timestamps();

            $table->foreign('perusahaan_id')
                ->references('id')
                ->cascadeOnUpdate()
                ->noActionOnUpdate()
                ->on('perusahaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('karyawan');
        Schema::dropIfExists('user_types');
    }
};
