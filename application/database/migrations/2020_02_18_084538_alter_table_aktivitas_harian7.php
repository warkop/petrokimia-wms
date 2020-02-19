<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAktivitasHarian7 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivitas_harian', function (Blueprint $table) {
            $table->integer('canceled')->nullable();
            $table->integer('cancelable')->nullable();
            $table->string('nopol')->nullable();
            $table->string('driver')->nullable();
            $table->string('posto')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aktivitas_harian', function (Blueprint $table) {
            $table->dropColumn('canceled');
            $table->dropColumn('cancelable');
            $table->dropColumn('nopol');
            $table->dropColumn('driver');
            $table->dropColumn('posto');
        });
    }
}
