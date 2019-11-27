<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAreaHousekeeperFoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_housekeeper_foto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_realisasi_housekeeper');
            $table->string('foto');
            $table->integer('size');
            $table->string('ekstensi');
            $table->string('file_enc');
            $table->integer('created_by');
            $table->datetime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('area_housekeeper_foto');
    }
}
