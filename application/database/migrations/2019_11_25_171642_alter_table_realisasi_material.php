<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRealisasiMaterial extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('realisasi_material', function (Blueprint $table) {
            $table->date('tanggal')->nullable();
            $table->dropColumn('id_realisasi');
            $table->dropColumn('bertambah');
            $table->dropColumn('berkurang');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('realisasi_material', function (Blueprint $table) {
            $table->dropColumn('tanggal');
            $table->integer('id_realisasi')->nullable();
            $table->integer('bertambah')->nullable();
            $table->integer('berkurang')->nullable();
        });
    }
}
