<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableMaterialTrans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('material_trans', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_material')->nullable();
            $table->integer('id_adjustment')->nullable();
            $table->date('tanggal')->nullable();
            $table->integer('tipe')->nullable();
            $table->integer('jumlah')->nullable();
            $table->string('alasan')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('material_trans');
    }
}
