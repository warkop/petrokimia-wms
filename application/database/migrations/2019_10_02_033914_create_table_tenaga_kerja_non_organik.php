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
            $table->increments('tenaga_kerja_non_organik_id');
            $table->integer('job_desk_id')->nullable();
            $table->string('nama_tenaga_kerja')->nullable();
            $table->string('nomor_hp')->nullable();
            $table->string('nomor_bpjs')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('tenaga_kerja_non_organik');
    }
}
