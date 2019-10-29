<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAktivitasGudang extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas_gudang', function (Blueprint $table) {
            $table->integer('id_aktivitas')->nullable();
            $table->integer('id_gudang')->nullable();
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
        Schema::dropIfExists('aktivitas_gudang');
    }
}
