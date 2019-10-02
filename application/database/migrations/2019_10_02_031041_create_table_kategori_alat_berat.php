<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableKategoriAlatBerat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kategori_alat_berat', function (Blueprint $table) {
            $table->increments('kategori_alat_berat_id');
            $table->string('nama_kategori_alat_berat');
            $table->integer('anggaran_alat_berat');
            $table->date('start_date');
            $table->date('end_date');
            $table->string('created_by')->nullable();
            $table->string('updated_by')->nullable();
            $table->string('deleted_by')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kategori_alat_berat');
    }
}
