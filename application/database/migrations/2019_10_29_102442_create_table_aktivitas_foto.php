<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAktivitasFoto extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas_foto', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_aktivitas_harian')->nullable();
            $table->integer('id_foto_jenis')->nullable();
            $table->string('foto', 100)->nullable();
            $table->float('size')->nullable();
            $table->string('lat', 50)->nullable();
            $table->string('lng', 50)->nullable();
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
        Schema::dropIfExists('aktivitas_foto');
    }
}
