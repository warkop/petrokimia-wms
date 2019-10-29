<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableLogActivity extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('log_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('id_user')->nullable();
            $table->string('modul', 50)->nullable();
            $table->integer('action')->nullable();
            $table->text('aktivitas')->nullable();
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
        Schema::dropIfExists('log_activity');
    }
}
