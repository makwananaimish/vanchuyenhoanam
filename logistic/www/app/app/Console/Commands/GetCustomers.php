<?php

namespace App\Console\Commands;

use App\Customer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class GetCustomers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-customers:run';

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
        $customers = Customer::get()
            ->sortByDesc('debt');

        $sort = [];

        foreach ($customers as $key => $customer) {
            // echo "id: {$customer->id} revenue: {$customer->revenue}"  . PHP_EOL;

            $sort[] = [
                'id' => $customer->id,
                'revenue' =>  $customer->revenue
            ];
        }


        $sort = collect($sort)->sortByDesc('revenue')->all();

        // var_dump($sort);

        Cache::forever('customer:sort-by-revenue', json_encode($sort));
    }
}
