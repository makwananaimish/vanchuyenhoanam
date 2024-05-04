<?php

namespace App\Console\Commands;

use App\Order;
use Illuminate\Console\Command;

class UpdateOrder12293 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:order-12293';

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
        $order = Order::find(12293);
        $order->rmb_to_vnd = 3450;
        $order->save();
    }
}
