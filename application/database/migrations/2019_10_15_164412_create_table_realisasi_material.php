<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableRealisasiMaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('realisasi_material', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_realisasi')->nullable();
            $table->integer('id_material')->nullable();
            $table->integer('bertambah')->nullable();
            $table->integer('berkurang')->nullable();
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
        Schema::dropIfExists('realisasi_material');
    }
}
