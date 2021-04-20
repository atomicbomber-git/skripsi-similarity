<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateSkripsiHashMaterializedView extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        \Illuminate\Support\Facades\DB::unprepared(/** @lang PostgreSQL */ <<<HERE
DROP MATERIALIZED VIEW skripsi_hashes
HERE);

        \Illuminate\Support\Facades\DB::unprepared(/** @lang PostgreSQL */ <<<HERE
CREATE MATERIALIZED VIEW IF NOT EXISTS skripsi_hashes AS (
    SELECT skripsi_id, array_agg(hash) AS hashes
    FROM kalimat_skripsi,
         unnest(hashes) AS hash
    WHERE kalimat_skripsi.teks NOT IN (SELECT teks FROM blacklist_kalimat)
    GROUP BY skripsi_id
);

CREATE INDEX IF NOT EXISTS hashes_gist ON skripsi_hashes USING gist(hashes _varchar_sml_ops)
HERE);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        \Illuminate\Support\Facades\DB::unprepared(/** @lang PostgreSQL */ <<<HERE
DROP MATERIALIZED VIEW skripsi_hashes
HERE);

        \Illuminate\Support\Facades\DB::unprepared(/** @lang PostgreSQL */ <<<HERE
CREATE MATERIALIZED VIEW IF NOT EXISTS skripsi_hashes AS (
    SELECT skripsi_id, array_agg(hash) AS hashes
    FROM kalimat_skripsi,
         unnest(hashes) AS hash
    GROUP BY skripsi_id
);

CREATE INDEX IF NOT EXISTS hashes_gist ON skripsi_hashes USING gist(hashes _varchar_sml_ops)
HERE
        );
    }
}
