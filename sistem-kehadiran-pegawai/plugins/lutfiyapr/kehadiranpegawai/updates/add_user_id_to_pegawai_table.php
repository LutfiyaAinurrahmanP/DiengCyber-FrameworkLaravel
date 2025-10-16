<?php namespace Lutfiyapr\KehadiranPegawai\Updates;

use Illuminate\Support\Facades\Schema;
use Winter\Storm\Database\Updates\Migration;

class AddUserIdToPegawaiTable extends Migration
{
    public function up()
    {
        Schema::table('lutfiyapr_kehadiranpegawai_pegawai', function($table)
        {
            $table->integer('user_id')->unsigned()->nullable()->after('id');

            $table->foreign('user_id')
                  ->references('id')
                  ->on('lutfiyapr_kehadiranpegawai_users')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('lutfiyapr_kehadiranpegawai_pegawai', function($table)
        {
            $table->dropForeign(['user_id']);
            $table->dropColumn('user_id');
        });
    }
}
