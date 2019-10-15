<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRealisasiHousekeeping extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realisasi_housekeeping', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_realisasi')->nullable();
            $table->integer('id_tkbm')->nullable();
            $table->integer('id_area')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('realisasi_housekeeping');
    }
}
