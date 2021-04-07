<?php

namespace App\Listeners;

use App\Events\SkripsiModified;
use App\Models\SkripsiHash;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class RefreshSkripsiHashes
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  SkripsiModified  $event
     * @return void
     */
    public function handle(SkripsiModified $event)
    {
        ray()->send("TEST TEST XXX");
        DB::unprepared("REFRESH MATERIALIZED VIEW " . (new SkripsiHash)->getTable());
    }
}
