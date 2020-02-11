<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFotoBuruh extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('foto_buruh', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_realisasi')->nullable();
            $table->string('foto')->nullable();
            $table->string('size')->nullable();
            $table->string('ekstensi')->nullable();
            $table->string('file_enc')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('foto_buruh');
    }
}
