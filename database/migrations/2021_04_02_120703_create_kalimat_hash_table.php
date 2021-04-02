<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKalimatHashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kalimat_hash', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('kalimat_skripsi_id')->index();
            $table->unsignedInteger("position")->index();
            $table->string("hash");
            $table->index(['kalimat_skripsi_id', 'position']);
            $table->timestamps();
            $table->foreign('kalimat_skripsi_id')->references('id')->on('kalimat_skripsi');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kalimat_hash');
    }
}
