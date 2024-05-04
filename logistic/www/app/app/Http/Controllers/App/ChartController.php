<?php

namespace App\Http\Controllers\App;

use App\Customer;
use App\Order;
use App\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ChartController extends BaseController
{
    public function chartCustomers(Request $request)
    {
        $from = $request->get('from');
        $to = $request->get('to');

        $from1 = null;
        $from2 = null;
        $to1 = null;
        $to2 = null;
        $rangeFormatted1 = '';
        $rangeFormatted2 = '';

        if ($from && $to) {
            $splitFrom = explode(' - ', $from);
            $splitTo = explode(' - ', $to);

            $from1 = Carbon::createFromFormat('m/d/Y', $splitFrom[0]);
            $to1 = Carbon::createFromFormat('m/d/Y', $splitFrom[1]);

            $from2 = Carbon::createFromFormat('m/d/Y', $splitTo[0]);
            $to2 = Carbon::createFromFormat('m/d/Y', $splitTo[1]);

            $rangeFormatted1 = "{$from1->format('m/d/Y')} - {$to1->format('m/d/Y')}";
            $rangeFormatted2 = "{$from2->format('m/d/Y')} - {$to2->format('m/d/Y')}";

            $totalCustomers1 = Customer::query()
                ->whereDate('created_at', '>=', $from1)
                ->whereDate('created_at', '<=', $to1)
                ->count();

            $totalCustomers2 = Customer::query()
                ->whereDate('created_at', '>=', $from2)
                ->whereDate('created_at', '<=', $to2)
                ->count();

            $from1 = $from1->format('d-m-Y');
            $from2 = $from2->format('d-m-Y');
            $to1 = $to1->format('d-m-Y');
            $to2 = $to2->format('d-m-Y');

            $trucksGroup = [
                [
                    'date' => "{$from1} -> {$to1}",
                    'total_customers' => $totalCustomers1,
                ],
                [
                    'date' => "{$from2} -> {$to2}",
                    'total_customers' => $totalCustomers2,
                ],
            ];
        } else {
            $trucksGroup = Truck::select(
                DB::raw('YEAR(departure_date) year, MONTH(departure_date) month'),
            )
                ->groupBy('year', 'month')
                ->orderBy('year', 'DESC')
                ->orderBy('month', 'DESC')
                ->get()
                ->map(function ($item) {
                    $totalCustomers = Customer::whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->count();

                    $_item['year'] = $item->year;
                    $_item['month'] = $item->month;
                    $_item['date'] = "{$item->month}-{$item->year}";
                    $_item['total_customers'] = $totalCustomers;

                    return $_item;
                });
        }

        return view('app.charts.customers', [

            'trucksGroup' => $trucksGroup,

            'from1' => $from1,
            'from2' => $from2,
            'to1' => $to1,
            'to2' => $to2,
            'rangeFormatted1' => $rangeFormatted1,
            'rangeFormatted2' => $rangeFormatted2,
        ]);
    }
}
