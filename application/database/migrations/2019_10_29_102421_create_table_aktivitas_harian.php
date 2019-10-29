<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAktivitasHarian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas_harian', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aktivitas')->nullable();
            $table->integer('id_gudang')->nullable();
            $table->integer('id_karu')->nullable();
            $table->integer('id_shift')->nullable();
            $table->integer('ref_number')->nullable();
            $table->integer('id_area')->nullable();
            $table->integer('id_alat_berat')->nullable();
            $table->string('ttd', 100)->nullable();
            $table->string('sistro', 100)->nullable();
            $table->datetime('approve')->nullable();
            $table->integer('kelayakan_before')->nullable();
            $table->integer('kelayakan_after')->nullable();
            $table->datetime('dikembalikan')->nullable();
            $table->datetime('created_at')->nullable();
            $table->integer('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aktivitas_harian');
    }
}
