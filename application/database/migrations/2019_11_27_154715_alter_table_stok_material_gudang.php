<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableStokMaterialGudang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stok_material_gudang', function (Blueprint $table) {
            $table->dropColumn('stok');
            $table->dropColumn('dipakai');
            $table->dropColumn('kosong');
            $table->dropColumn('rusak');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stok_material_gudang', function (Blueprint $table) {
            $table->integer('stok')->nullable();
            $table->integer('dipakai')->nullable();
            $table->integer('kosong')->nullable();
            $table->integer('rusak')->nullable();
        });
    }
}
