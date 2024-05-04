<?php

namespace App\Providers;

use App\Customer;
use App\Order;
use App\Repositories\CustomerRepository;
use App\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('only-admin', function ($user) {
            return $user->is_admin;
        });

        Gate::define('only-customer', function ($user) {
            return CustomerRepository::check();
        });

        Gate::define('has-transaction', function ($user, $transaction) {
            if ($user->is_admin) return true;

            return optional(CustomerRepository::user())->id === $transaction->customer_id;
        });

        Gate::define('has-permissions', function ($user, $action) {
            if ($user->is_admin) return true;

            if (Arr::has($user->permissions, $action)) {
                if ($user->permissions[$action]) return true;
            }

            return false;
        });

        Gate::define('update-order-noname', function ($user, $order) {
            if ($user->is_admin) return true;

            if (
                $user->role === User::ROLE_ACCOUNTANT ||
                $user->role === User::ROLE_VN_INVENTORY ||
                $user->role === User::ROLE_CN_INVENTORY
            ) {
                if ($order->customer->code === Customer::NONAME_CODE) return true;
            }

            return false;
        });

        Gate::define('can-delete-order', function ($user) {
            return $user->role !== User::ROLE_SELLER;
        });
    }
}
