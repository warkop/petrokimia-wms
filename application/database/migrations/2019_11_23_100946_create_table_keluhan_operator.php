<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKeluhanOperator extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('keluhan_operator', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_operator');
            $table->integer('id_keluhan');
            $table->text('keterangan');
            $table->integer('created_by');
            $table->datetime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('keluhan_operator');
    }
}
