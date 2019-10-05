<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableListAlatBerat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('alat_berat', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_kategori')->nullable();
            $table->string('nomor_lambung')->nullable();
            $table->string('nomor_polisi')->nullable();
            $table->string('status', 1)->default(1);
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
        Schema::dropIfExists('alat_berat');
    }
}
