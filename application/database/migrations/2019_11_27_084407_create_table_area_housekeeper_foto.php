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
            $table->integer('id_realisasi_housekeeper')->nullable();
            $table->string('foto')->nullable();
            $table->integer('size')->nullable();
            $table->string('ekstensi')->nullable();
            $table->string('file_enc')->nullable();
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
        Schema::dropIfExists('area_housekeeper_foto');
    }
}
