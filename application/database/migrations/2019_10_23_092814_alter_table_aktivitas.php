<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableAktivitas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->integer('produk_rusak')->nullable();
            $table->integer('kelayakan')->nullable();
            $table->integer('butuh_biaya')->nullable();
            $table->integer('peminjaman')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aktivitas', function (Blueprint $table) {
            $table->dropColumn('produk_rusak');
            $table->dropColumn('kelayakan');
            $table->dropColumn('butuh_biaya');
            $table->dropColumn('peminjaman');
        });
    }
}
