<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableRencanaHarian extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rencana_harian', function (Blueprint $table) {
            $table->integer('id_gudang')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rencana_harian', function (Blueprint $table) {
            $table->dropColumn('id_gudang');
        });
    }
}
