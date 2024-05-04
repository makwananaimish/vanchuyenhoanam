<?php

namespace App\Observers;

use App\Location;
use App\Order;
use App\Services\Webhook;
use App\Truck;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class TruckObserver
{
    /**
     * Handle the truck "created" event.
     *
     * @param  \App\Truck  $truck
     * @return void
     */
    public function created(Truck $truck)
    {
        if ($truck->isDirty('cost')) {
            $truck->updateCostPerCubicMetersOfAllOrders();
            $truck->updateCostPerWeightOfAllOrders();
        }
    }

    /**
     * Handle the truck "updated" event.
     *
     * @param  \App\Truck  $truck
     * @return void
     */
    public function updated(Truck $truck)
    {
        if ($truck->isDirty('cost')) {
            $truck->updateCostPerCubicMetersOfAllOrders();
            $truck->updateCostPerWeightOfAllOrders();
        }

        // if ($truck->isDirty('current_location_id')) {
        //     if (optional($truck->currentLocation)->name === Location::VIETNAM_INVENTORY) {
        //         $truck->updateInVietnameseInventoryDateOfAllOrders();
        //     }
        // }

        if ($truck->isDirty('current_location_id')) {
            if (
                optional($truck->currentLocation)->name ===
                Location::VIETNAM_INVENTORY_2
            ) {
                $webhook = new Webhook();

                Log::info("updated truck #" . $truck->id);
                // Log::info((array) $truck->currentLocation);

                $orders = $truck->orders;

                $customers = [];

                foreach ($orders as $key => $order) {
                    Log::info("order #" . $order->id);
                    Log::info("customer_id #" . $order->customer_id);

                    $customer = Arr::get($customers, $order->customer_id);
                    $customerObj = $order->customer;

                    if ($customer) {
                        $customers[$order->customer_id]['money_estimated'] += $order->debt;
                        $customers[$order->customer_id]['final_amount'] += $order->debt;
                        $customers[$order->customer_id]['orders'][] = [
                            'id' => $order->id,
                            'status' => $order->status_text
                        ];
                    } else {
                        $query = Order::with([
                            'truck.currentLocation',
                            'payments',
                            'packs.order.truck.currentLocation',
                            'declarations',
                            'truck',
                            'customer',
                        ]);

                        $debt =
                            $query
                            ->where('customer_id', $customerObj->id)
                            ->orderBy('created_at')
                            ->get()
                            ->filter(function ($order) {
                                return $order->status_text === Order::STATUS_TEXT_WAIT_FOR_PAYING;
                            })
                            ->sum(function ($order) {
                                return round($order->debt);
                            });

                        $customers[$order->customer_id] = [
                            'customer_id' => $order->customer_id,
                            'name' => $customerObj->phone,
                            'money_estimated' => $order->fare,
                            'to_address' => $customerObj->address,
                            'final_amount' => $order->debt,
                            'balance' => $customerObj->balance,
                            'unpaid_debt' => $debt,
                            'orders' => [
                                [
                                    'id' => $order->id,
                                    'status' => $order->status_text,
                                ]
                            ]
                        ];
                    }
                }

                Log::info((array) $customers);

                $webhook->postToWebhookDotSite($customers);
            }
        }
    }

    /**
     * Handle the truck "deleted" event.
     *
     * @param  \App\Truck  $truck
     * @return void
     */
    public function deleted(Truck $truck)
    {
        //
    }

    /**
     * Handle the truck "restored" event.
     *
     * @param  \App\Truck  $truck
     * @return void
     */
    public function restored(Truck $truck)
    {
        //
    }

    /**
     * Handle the truck "force deleted" event.
     *
     * @param  \App\Truck  $truck
     * @return void
     */
    public function forceDeleted(Truck $truck)
    {
        //
    }
}
