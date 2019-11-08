<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAlatBeratKat2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('alat_berat_kat', function (Blueprint $table) {
            $table->dropColumn('anggaran');
            $table->dropColumn('forklift');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('alat_berat_kat', function (Blueprint $table) {
            $table->float('anggaran')->nullable();
            $table->integer('forklift')->nullable();
        });
    }
}
