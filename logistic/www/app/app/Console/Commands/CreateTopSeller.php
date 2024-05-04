<?php

namespace App\Console\Commands;

use App\TopSeller;
use App\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CreateTopSeller extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'top_seller:create';

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
        $sellers = User::where('role', User::ROLE_SELLER)
            ->get();

        $currentMonth = Carbon::now();
        $lastYear = $currentMonth->copy()->subYear();

        while ($currentMonth->greaterThanOrEqualTo($lastYear)) {
            $month = $currentMonth->get('month');
            $year = $currentMonth->get('year');

            foreach ($sellers as $key => $seller) {
                echo "Create {$month} {$year} #{$seller->id}" . PHP_EOL;

                TopSeller::query()->updateOrCreate([
                    'month' => $month,
                    'year' => $year,
                    'seller_id' => $seller->id,
                ], [
                    'month' => $month,
                    'year' => $year,
                    'commission' => $this->totalCommission($seller->id, $month, $year)['total'],
                    'seller_id' => $seller->id
                ]);
            }

            $currentMonth->subMonth();
        }
    }

    public function totalCommission($id, $month, $year)
    {
        $query = User::with([
            'customers' => function ($subQuery) use ($month, $year) {
                $subQuery
                    ->with([
                        'orders' => function ($q) use ($month, $year) {
                            $q
                                ->whereHas('payments', function ($queryPayments) use ($month, $year) {
                                    $queryPayments
                                        ->whereMonth('created_at',  $month)
                                        ->whereYear('created_at', $year)
                                        ->whereNull('deleted_at');
                                })
                                ->whereRaw('(taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)');
                        },
                    ]);
            },
        ])
            ->where('role', User::ROLE_SELLER)
            ->where('id', $id);

        $seller = $query->first();

        $total = $seller->customers->sum(function ($customer) {
            return $customer->orders->sum('commission');
        });

        $formattedTotal = number_format(
            $total,
            0,
            '',
            '.',
        );

        return [
            'total' => $total,
            'formatted_total' => $formattedTotal
        ];
    }
}
