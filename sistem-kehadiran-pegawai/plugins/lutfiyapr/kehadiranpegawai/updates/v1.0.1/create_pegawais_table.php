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
        Schema::create('lutfiyapr_kehadiranpegawai_pegawai', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('nip', 50)->unique();
            $table->string('nama', 100);
            $table->string('jabatan', 100);
            $table->string('email')->unique();
            $table->string('no_telp', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->enum('status', ['aktif', 'non-aktif'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('lutfiyapr_kehadiranpegawai_pegawai');
    }
};
