<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAktivitasKeluhanGp extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivitas_keluhan_gp', function (Blueprint $table) {
            $table->dropColumn('jumlah');
        });

        Schema::table('aktivitas_keluhan_gp', function (Blueprint $table) {
            $table->double('jumlah')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aktivitas_keluhan_gp', function (Blueprint $table) {
            $table->dropColumn('jumlah');
        });

        Schema::table('aktivitas_keluhan_gp', function (Blueprint $table) {
            $table->integer('jumlah')->nullable();
        });
    }
}
