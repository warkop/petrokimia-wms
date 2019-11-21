<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAktivitasKelayakanFoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas_kelayakan_foto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aktivitas_harian')->nullable();
            $table->integer('jenis')->default(1)->nullable();
            $table->string('foto')->nullable();
            $table->string('size')->nullable();
            $table->string('ekstensi')->nullable();
            $table->string('file_enc')->nullable();
            $table->integer('created_by')->nullable();
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
        Schema::dropIfExists('aktivitas_kelayakan_foto');
    }
}
