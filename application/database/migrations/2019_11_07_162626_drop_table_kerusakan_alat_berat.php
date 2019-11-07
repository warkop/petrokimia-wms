<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTableKerusakanAlatBerat extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('kerusakan_alat_berat');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::create('kerusakan_alat_berat', function (Blueprint $table) {
            $table->integer('id_kerusakan');
            $table->integer('id_alat_berat_kat');
        });
    }
}
