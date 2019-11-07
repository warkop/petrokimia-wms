<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAktivitasAlatBerat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas_alat_berat', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_kategori_alat_berat')->nullable();
            $table->integer('id_aktivitas')->nullable();
            $table->float('anggaran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aktivitas_alat_berat');
    }
}
