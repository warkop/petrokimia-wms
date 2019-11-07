<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLaporanKerusakanFoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_kerusakan_foto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_laporan')->nullable();
            $table->string('file_ori')->nullable();
            $table->string('size')->nullable();
            $table->string('ekstensi')->nullable();
            $table->string('file_enc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_kerusakan_foto');
    }
}
