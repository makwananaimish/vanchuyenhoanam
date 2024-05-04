<?php

namespace App\Console\Commands;

use App\Customer;
use App\Order;
use App\TopCustomer;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateTopCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'top_customer:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $customers = Customer::get();

        $currentMonth = Carbon::now();
        $lastYear = $currentMonth->copy()->subYear();

        while ($currentMonth->greaterThanOrEqualTo($lastYear)) {
            $month = $currentMonth->get('month');
            $year = $currentMonth->get('year');

            foreach ($customers as $key => $customer) {
                echo "Create {$month} {$year} #{$customer->id}" . PHP_EOL;

                TopCustomer::updateOrCreate([
                    'month' => $month,
                    'year' => $year,
                    'customer_id' => $customer->id,
                ], [
                    'month' => $month,
                    'year' => $year,
                    'revenue' => $this->getRevenue($customer->id, $month, $year),
                    'customer_id' => $customer->id
                ]);
            }

            $currentMonth->subMonth();
        }
    }

    public function getCompletedOrders($customerId, $month, $year)
    {
        $query = Order::with([
            'truck.currentLocation',
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
            'messages.messageViews'
        ]);

        return $query
            ->where('customer_id', $customerId)
            ->whereMonth('delivery_date', $month)
            ->whereYear('delivery_date', $year)
            ->get()
            ->filter(function ($order) {
                return $order->status_text === Order::STATUS_TEXT_COMPLETED;
            });
    }

    public function getRevenue($customerId, $month, $year)
    {
        $completedOrders = $this->getCompletedOrders($customerId, $month, $year);

        return $completedOrders->sum('revenue');
    }
}
