<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLaporanKerusakan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('laporan_kerusakan', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_kerusakan')->nullable();
            $table->integer('id_alat_berat')->nullable();
            $table->integer('id_shift')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('jenis')->nullable();
            $table->datetime('jam_rusak')->nullable();
            $table->integer('created_by')->nullable();
            $table->datetime('created_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('laporan_kerusakan');
    }
}
