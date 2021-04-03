<?php

namespace App\Providers;

use Illuminate\Database\Query\Grammars\PostgresGrammar;
use Illuminate\Database\Schema\Grammars\Grammar;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Fluent;
use Illuminate\Support\ServiceProvider;
use Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Paginator::useBootstrap();

        Grammar::macro("typeTextArray", fn () => "text[]");
    }
}
