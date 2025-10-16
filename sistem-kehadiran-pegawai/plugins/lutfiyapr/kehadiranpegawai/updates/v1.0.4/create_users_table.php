<?php

use Winter\Storm\Database\Schema\Blueprint;
use Winter\Storm\Database\Updates\Migration;
use Winter\Storm\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('lutfiyapr_kehadiranpegawai_users', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('pegawai_id')->unsigned()->nullable();
            $table->string('username', 50)->unique();
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'pegawai'])->default('pegawai');
            $table->string('api_token', 80)->unique()->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->timestamps();

            // Foreign key
            $table->foreign('pegawai_id')
                ->references('id')
                ->on('lutfiyapr_kehadiranpegawai_pegawai')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lutfiyapr_kehadiranpegawai_users');
    }
};
