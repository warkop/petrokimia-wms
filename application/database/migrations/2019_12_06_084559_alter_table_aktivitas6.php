<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAktivitas6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->dropColumn('penerimaan_gp');
        });

        Schema::table('aktivitas', function (Blueprint $table) {
            $table->integer('penerimaan_gi')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->dropColumn('penerimaan_gi');
        });

        Schema::table('aktivitas', function (Blueprint $table) {
            $table->integer('penerimaan_gp')->nullable();
        });
    }
}
