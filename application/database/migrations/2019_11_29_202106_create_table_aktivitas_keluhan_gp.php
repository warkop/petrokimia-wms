<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAktivitasKeluhanGp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas_keluhan_gp', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aktivitas_harian');
            $table->integer('id_material');
            $table->integer('jumlah');
            $table->text('keluhan');
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
        Schema::dropIfExists('aktivitas_keluhan_gp');
    }
}
