<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSkripsiFingerprintHashTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('skripsi_fingerprint_hash', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('skripsi_id')->index();
            $table->unsignedInteger("position")->index();
            $table->string("hash");

            $table->index(['skripsi_id', 'position']);
            $table->foreign('skripsi_id')->references('id')->on('skripsi');
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
        Schema::dropIfExists('skripsi_fingerprint_hash');
    }
}
