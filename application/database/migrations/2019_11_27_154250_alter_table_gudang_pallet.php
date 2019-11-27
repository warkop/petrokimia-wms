<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterTableGudangPallet extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('gudang_pallet', function (Blueprint $table) {
            $table->renameColumn('status_pallet', 'status')->change();
        });

        Schema::rename('gudang_pallet', 'gudang_stok');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::rename('gudang_stok', 'gudang_pallet');

        Schema::table('gudang_pallet', function (Blueprint $table) {
            $table->renameColumn('status', 'status_pallet')->change();
        });
    }
}
