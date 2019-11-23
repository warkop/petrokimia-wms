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
            $table->integer('id_operator')->nullable();
            $table->integer('id_keluhan')->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('created_by')->nullable();
            $table->datetime('created_at')->nullable();
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
