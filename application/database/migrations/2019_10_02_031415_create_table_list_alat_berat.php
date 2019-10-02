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
        Schema::create('list_alat_berat', function (Blueprint $table) {
            $table->increments('list_alat_berat_id');
            $table->integer('kategori_alat_berat_id')->nullable();
            $table->string('nomor_lambung')->nullable();
            $table->string('nomor_polisi')->nullable();
            $table->string('status', 1)->default(1);
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
        Schema::dropIfExists('list_alat_berat');
    }
}
