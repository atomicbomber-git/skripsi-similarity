<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    const CAN_ACCESS_BANK_SKRIPSI_MAHASISWA = "CAN_ACCESS_BANK_SKRIPSI_MAHASISWA";
    const CAN_ACCESS_MAHASISWA_MANAGEMENT_FEATURES = "CAN_ACCESS_MAHASISWA_MANAGEMENT_FEATURES";
    const CAN_ACCESS_MAHASISWA_DASHBOARD = "CAN_ACCESS_MAHASISWA_DASHBOARD";
    const CAN_ACCESS_BLACKLIST_KALIMAT_MANAGEMENT = "CAN_ACCESS_BLACKLIST_KALIMAT_MANAGEMENT";

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
        Gate::define(self::CAN_ACCESS_BLACKLIST_KALIMAT_MANAGEMENT, function (User $user) {
            return $user->level === User::LEVEL_ADMIN;
        });

        Gate::define(self::CAN_ACCESS_MAHASISWA_MANAGEMENT_FEATURES, function (User $user) {
            return $user->level === User::LEVEL_ADMIN;
        });

        Gate::define(self::CAN_ACCESS_BANK_SKRIPSI_MAHASISWA, function (User $user) {
            return $user->level === User::LEVEL_MAHASISWA;
        });

        Gate::define(self::CAN_ACCESS_MAHASISWA_DASHBOARD, function (User $user) {
            return $user->level === User::LEVEL_MAHASISWA;
        });

        $this->registerPolicies();
    }
}
