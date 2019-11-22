<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableGudang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gudang', function (Blueprint $table) {
            $table->dropColumn('id_plant');
            $table->dropColumn('id_sloc');
            
        });
        Schema::table('gudang', function (Blueprint $table) {
            $table->string('id_plant')->nullable();
            $table->string('id_sloc')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('gudang', function (Blueprint $table) {
            $table->dropColumn('id_plant');
            $table->dropColumn('id_sloc');
        });
        Schema::table('gudang', function (Blueprint $table) {
            $table->integer('id_plant')->nullable();
            $table->integer('id_sloc')->nullable();
        });
    }
}
