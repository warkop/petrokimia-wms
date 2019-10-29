<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAktivitasArea extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas_area', function (Blueprint $table) {
            $table->integer('id_aktivitas_harian')->nullable();
            $table->integer('id_area_stok')->nullable();
            $table->float('jumlah')->nullable();
            $table->datetime('created_at')->nullable();
            $table->integer('created_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('aktivitas_area');
    }
}
