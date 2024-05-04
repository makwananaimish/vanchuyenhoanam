<?php

namespace App\Http\Controllers\App;

use App\User;
use Illuminate\Support\Carbon;

class SellerController extends BaseController
{
    public function index()
    {
        $month = request('month');
        $code = request('code');

        $query = User::with([
            'customers' => function ($subQuery) use ($month, $code) {
                $subQuery
                    ->with([
                        'orders' => function ($q) use ($month) {
                            if ($month) {
                                $_month = Carbon::createFromFormat('Y-m', $month);

                                $q
                                    // ->whereMonth('created_at',  $_month->month)
                                    // ->whereYear('created_at', $_month->year)
                                    ->whereHas('payments', function ($queryPayments) use ($_month) {
                                        $queryPayments
                                            ->whereMonth('created_at',  $_month->month)
                                            ->whereYear('created_at', $_month->year)
                                            ->whereNull('deleted_at');
                                    })
                                    ->whereRaw('(taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)');
                                // ->whereRaw('(SELECT COUNT(*) FROM view_orders WHERE id = orders.id AND debt <= 1 ) > 0');
                            } else {
                                // $q
                                //     ->where('taxes1', '>', 0)
                                //     ->orWhere('taxes2', '>', 0)
                                //     ->orWhere('cost_vietnam', '>', 0)
                                //     ->orWhere('fare_unit_by_weight', '>', 0)
                                //     ->orWhere('fare_unit_by_cubic_meters', '>', 0);

                                $q
                                    ->whereRaw('(taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)');
                                // ->whereRaw('(SELECT COUNT(*) FROM view_orders WHERE id = orders.id AND debt <= 1 ) > 0');
                            }
                        },
                    ])
                    // ->where('id', 229)
                    // ->where('id', 234)
                    // ->where('id', 5)
                ;
            },
        ])
            // ->where('id', 28)
            // ->where('id', 10)
            ->where('role', User::ROLE_SELLER);

        if (auth()->user()->role === User::ROLE_SELLER) {
            $query = $query->where('id', auth()->id());
        }

        if (!is_null($code)) {
            $query = $query
                ->whereHas('customers.orders', function ($q) use ($code) {
                    $q->where('code', $code);
                });
        }

        $sellers = $query
            ->paginate();

        // dd($sellers);

        return view('app.seller.index', [
            'sellers' => $sellers,
            'month' => $month
        ]);
    }

    public function find($id)
    {
        $month = request('month');
        $code = request('code');

        $query = User::with([
            'customers' => function ($subQuery) use ($month, $code) {
                $subQuery
                    ->with([
                        'orders' => function ($q) use ($month) {
                            if ($month) {
                                $_month = Carbon::createFromFormat('Y-m', $month);

                                $q
                                    ->whereHas('payments', function ($queryPayments) use ($_month) {
                                        $queryPayments
                                            ->whereMonth('created_at',  $_month->month)
                                            ->whereYear('created_at', $_month->year)
                                            ->whereNull('deleted_at');
                                    })
                                    ->whereRaw('(taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)');
                            } else {


                                $q
                                    ->whereRaw('(taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)');
                            }
                        },
                    ]);
            },
        ])
            ->where('role', User::ROLE_SELLER)
            ->where('id', $id);

        if (auth()->user()->role === User::ROLE_SELLER) {
            $query = $query->where('id', auth()->id());
        }

        if (!is_null($code)) {
            $query = $query
                ->whereHas('customers.orders', function ($q) use ($code) {
                    $q->where('code', $code);
                });
        }

        $seller = $query->first();

        return response()->json($seller);
    }

    public function totalCommission($id)
    {
        $month = request('month');
        $code = request('code');

        $query = User::with([
            'customers' => function ($subQuery) use ($month, $code) {
                $subQuery
                    ->with([
                        'orders' => function ($q) use ($month) {
                            if ($month) {
                                $_month = Carbon::createFromFormat('Y-m', $month);

                                $q
                                    ->whereHas('payments', function ($queryPayments) use ($_month) {
                                        $queryPayments
                                            ->whereMonth('created_at',  $_month->month)
                                            ->whereYear('created_at', $_month->year)
                                            ->whereNull('deleted_at');
                                    })
                                    ->whereRaw('(taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)');
                            } else {


                                $q
                                    ->whereRaw('(taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)');
                            }
                        },
                    ]);
            },
        ])
            ->where('role', User::ROLE_SELLER)
            ->where('id', $id);

        if (auth()->user()->role === User::ROLE_SELLER) {
            $query = $query->where('id', auth()->id());
        }

        if (!is_null($code)) {
            $query = $query
                ->whereHas('customers.orders', function ($q) use ($code) {
                    $q->where('code', $code);
                });
        }

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

        return response()->json([
            'total' => $total,
            'formatted_total' => $formattedTotal
        ]);
    }
}
