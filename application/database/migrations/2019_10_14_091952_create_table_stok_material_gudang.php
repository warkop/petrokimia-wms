<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableStokMaterialGudang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stok_material_gudang', function (Blueprint $table) {
            $table->integer('id_gudang')->nullable();
            $table->integer('id_material')->nullable();
            $table->integer('stok_min')->nullable();
            $table->integer('stok')->nullable();
            $table->integer('dipakai')->nullable();
            $table->integer('kosong')->nullable();
            $table->integer('rusak')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stok_material_gudang');
    }
}
