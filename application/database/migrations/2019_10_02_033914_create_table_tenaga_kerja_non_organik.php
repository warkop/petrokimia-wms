<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableTenagaKerjaNonOrganik extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tenaga_kerja_non_organik', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('job_desk_id')->nullable();
            $table->string('nama')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('nomor_bpjs')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
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
        Schema::dropIfExists('tenaga_kerja_non_organik');
    }
}
