<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAktifitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('aktivitas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nama')->nullable();
            $table->integer('produk_stok')->nullable();
            $table->integer('pallet_stok')->nullable();
            $table->integer('pallet_dipakai')->nullable();
            $table->integer('pallet_kosong')->nullable();
            $table->integer('upload_foto')->nullable();
            $table->integer('fifo')->nullable();
            $table->integer('butuh_alat_berat')->nullable();
            $table->integer('connect_sistro')->nullable();
            $table->integer('pengaruh_tgl_produksi')->nullable();
            $table->integer('butuh_tkbm')->nullable();
            $table->integer('pengiriman')->nullable();
            $table->integer('internal_gudang')->nullable();
            $table->integer('tanda_tangan')->nullable();
            $table->integer('butuh_approval')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
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
        Schema::dropIfExists('aktivitas');
    }
}
