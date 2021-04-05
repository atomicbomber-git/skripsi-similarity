<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKalimatSkripsiTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kalimat_skripsi', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('skripsi_id')->index();
            $table->longText('teks');
            $table->addColumn("varcharArray", "hashes");
            $table->timestamps();
            $table->foreign('skripsi_id')->references('id')->on('skripsi');
        });

        DB::unprepared("CREATE INDEX IF NOT EXISTS hashes_gist ON kalimat_skripsi USING gist(hashes _varchar_sml_ops)");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('kalimat_skripsi');
    }
}
