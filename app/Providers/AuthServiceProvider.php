<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    const CAN_ACCESS_BANK_SKRIPSI_MAHASISWA = "CAN_ACCESS_BANK_SKRIPSI_MAHASISWA";

    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        Gate::define(self::CAN_ACCESS_BANK_SKRIPSI_MAHASISWA, function (User $user) {
            return $user->level === User::LEVEL_MAHASISWA;
        });

        $this->registerPolicies();
    }
}
