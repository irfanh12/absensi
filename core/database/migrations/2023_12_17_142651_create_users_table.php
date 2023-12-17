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
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->unsignedBigInteger('created_at');
            $table->unsignedBigInteger('updated_at')->nullable();
            // $table->timestamps();

            $table->foreign('id')
                ->references('id')
                ->cascadeOnUpdate()
                ->cascadeOnDelete()
                ->on('karyawan');
        });

        Schema::create('user_types', function (Blueprint $table) {
            $table->bigInteger('id');
            $table->string('type');

            $table->index('id', 'karyawan_type_id_foreign');
        });

        Schema::table('karyawan', function (Blueprint $table) {

            $table->foreign('type_id')
                ->references('id')
                ->noActionOnUpdate()
                ->noActionOnDelete()
                ->on('user_types');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('user_types');
    }
};
