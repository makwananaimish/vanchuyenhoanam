<?php

namespace App\Providers;

use App\Customer;
use App\Location;
use App\NotifyAddress;
use App\Observers\CustomerObserver;
use App\Observers\OrderObserver;
use App\Observers\TransactionObserver;
use App\Observers\TruckObserver;
use App\Order;
use App\Transaction;
use App\Truck;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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
        Truck::observe(TruckObserver::class);
        Transaction::observe(TransactionObserver::class);
        Order::observe(OrderObserver::class);
        Customer::observe(CustomerObserver::class);

        $locationId = optional(Auth::user())->location_id;

        $inChinaLocations = Location::where('type', Location::IN_CHINA)
            ->whereRaw(is_null($locationId)  ? "id IS NOT NULL" : "id = $locationId")
            ->get();

        $inVietnamLocations = Location::whereRaw("( type = " . Location::IN_VIETNAM . " OR type IS NULL ) AND deleted_at IS NULL")
            ->where('name', '!=', 'Hoàn Thành')
            ->whereRaw(is_null($locationId)  ? "id IS NOT NULL" : "id = $locationId")
            ->get();

        view()->composer('*', function ($view) use ($inChinaLocations, $inVietnamLocations) {
            try {
                $routeName = Route::getCurrentRoute()->getName();
                $view->with('routeName', $routeName);

                $view->with('_inChinaLocations', $inChinaLocations);
                $view->with('_inVietnamLocations', $inVietnamLocations);
            } catch (\Throwable $th) {
                //throw $th;
            }
        });

        $notifyAddresses = NotifyAddress::query()
            ->with([
                'order.customer'
            ])
            ->whereHas('order', function ($query) {
                $query
                    ->whereNotNull('delivery_date')
                    ->whereDate('delivery_date', '>=', Carbon::now()->subDays(2));
            })
            ->orWhereHas('order', function ($query) {
                $query->whereNull('delivery_date');
            })
            ->orderBy('id', 'DESC')
            ->get()
            ->sort(function ($a, $b) {
                if ($a->order->status_text === Order::STATUS_TEXT_UNDELIVERED) {
                    return false;
                }

                return true;
            });

        View::share('notifyAddresses', $notifyAddresses);
    }
}
