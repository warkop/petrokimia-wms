<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableGudangPallet2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gudang_pallet', function (Blueprint $table) {
            $table->dropColumn('status_pallet');
            $table->integer('status')->nullable()->comment('stok = 1, dipakai = 2, kosong = 3, rusak = 4');
        });

        Schema::rename('gudang_pallet', 'gudang_stok');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('gudang_stok', 'gudang_pallet');

        Schema::table('gudang_pallet', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->integer('status_pallet')->nullable()->comment('');
        });
    }
}
