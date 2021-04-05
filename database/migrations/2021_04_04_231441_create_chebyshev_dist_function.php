<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChebyshevDistFunction extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
//        DB::unprepared(/** @lang PostgreSQL */ <<<HERE
//CREATE OR REPLACE FUNCTION chebyshev_dist(varchar[], varchar[]) RETURNS bigint
//    AS $$
//    WITH
//    p AS (SELECT hash, count(hash) AS count FROM unnest($1) AS hash GROUP BY hash),
//    q AS (SELECT hash, count(hash) AS count FROM unnest($2) AS hash GROUP BY hash)
//SELECT max(abs(coalesce(p.count, 0) - coalesce(q.count, 0)))  FROM
//    p FULL OUTER JOIN q ON p.hash = q.hash
//    $$
//    LANGUAGE SQL
//    IMMUTABLE;
//END;
//HERE
//);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        DB::unprepared(/** @lang PostgreSQL */ "DROP FUNCTION chebyshev_dist");
    }
}
