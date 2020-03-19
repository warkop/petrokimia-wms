<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAktivitas11 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->integer('status_aktivitas')->nullable(); //1 = keluar, 2 = masuk
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
            $table->dropColumn('status_aktivitas');
        });
    }
}
