<?php

namespace App\Console\Commands;

use App\Truck;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class GetCompletedTrucks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truck:get-list-completed';

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
        echo "Get incompletedTruckIds" . PHP_EOL;

        $trucks = Truck::all();
        // $trucks = Truck::query()->where('id', '<=', 10)->get();
        $incompletedTruckIds = [];

        foreach ($trucks as $key => $truck) {
            // Log::info($truck->debt);
            echo "checking: {$truck->name}, debt: {$truck->debt}" . PHP_EOL;

            foreach ($truck->orders as $order) {
                echo " order #{$order->id} debt: {$order->debt}" . PHP_EOL;
            }

            if (
                $truck->debt > 10000
                && $truck->name !== 'KK8792-A2299'
                && $truck->name !== 'HAICHANG PD7-45'
                && $truck->name !== 'HIẾUN.A 18.10'
                && $truck->name !== 'KV9892-DB636 17/10/2022'
            ) {
                echo "name: {$truck->name}, debt: {$truck->debt}" . PHP_EOL;
                $incompletedTruckIds[] = $truck->id;
            }
        }

        Cache::forever('incompletedTruckIds', json_encode($incompletedTruckIds));

        Log::info($incompletedTruckIds);
    }
}
