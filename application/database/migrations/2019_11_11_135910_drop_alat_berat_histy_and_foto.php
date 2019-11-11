<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropAlatBeratHistyAndFoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('alat_berat_history');
        Schema::dropIfExists('alat_berat_history_foto');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('alat_berat_history', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_alat_berat_kerusakan')->nullable();
            $table->datetime('waktu')->nullable();
            $table->text('keterangan')->nullable();
        });

        Schema::create('alat_berat_history_foto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_ab_history')->nullable();
            $table->string('foto', 100)->nullable();
        });
    }
}
