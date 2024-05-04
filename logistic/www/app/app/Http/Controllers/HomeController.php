<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Order;
use App\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:web,customer', 'is_customer:web,customer']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return redirect()->route('home', ['#address-notification']);
    }

    public function home(Request $request)
    {
        $totalTrucks = Truck::count();
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();

        $trucksGroup = Truck::select(
            DB::raw('YEAR(departure_date) year, MONTH(departure_date) month'),
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->get()
            ->map(function ($item) {
                $orders = Order::with([
                    'packs',
                    'payments',
                ])
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                $ordersBangTuong = Order::with([
                    'packs',
                    'payments',
                ])
                    ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                $totalCustomersBangTuong = Order::query()
                    ->select(DB::raw('customer_id'))
                    ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->groupBy('customer_id')
                    ->count();

                if (isSeller()) {
                    $orders = Order::with([
                        'packs',
                        'payments',
                    ])
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();

                    $ordersBangTuong = Order::with([
                        'packs',
                        'payments',
                    ])
                        ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();

                    $totalCustomersBangTuong = Order::query()
                        ->select(DB::raw('customer_id'))
                        ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->groupBy('customer_id')
                        ->count();
                }

                $trucks = Truck::whereYear('departure_date', $item->year)
                    ->whereMonth('departure_date', $item->month)
                    ->get();

                $revenue = $orders->sum('revenue');
                $revenue = round($revenue);

                $cost = $trucks->sum('total_cost');
                $cost = round($cost);

                $debt = $orders->sum('debt');
                $debt = round($debt);

                $totalTrucks = $trucks->count();
                $totalCustomers = Customer::whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->count();
                $totalOrders = $orders->count();
                $totalOrdersBangTuong = $ordersBangTuong->count();

                $_item['revenue'] = $revenue;
                $_item['cost'] = $cost;
                $_item['debt'] = $debt;
                $_item['year'] = $item->year;
                $_item['month'] = $item->month;
                $_item['total_trucks'] = $totalTrucks;
                $_item['total_customers'] = $totalCustomers;
                $_item['total_customers_bang_tuong'] = $totalCustomersBangTuong;
                $_item['total_orders'] = $totalOrders;
                $_item['total_orders_bang_tuong'] = $totalOrdersBangTuong;

                return $_item;
            });

        $months = request('months', 3);
        $totalRevenue = $trucksGroup->take($months)->sum('revenue');
        $totalCost = $trucksGroup->take($months)->sum('cost');
        $totalDebt = $trucksGroup->take($months)->sum('debt');
        $totalOrders = $trucksGroup->take($months)->sum('total_orders');

        // $totalTrucks = 0;
        // $totalCustomers = 0;
        // $totalOrders = 0;
        // $totalRevenue = 0;
        // $totalCost = 0;
        // $totalDebt = 0;
        // $trucksGroup = [];

        $trucksGroupBangTuong = Truck::select(
            DB::raw('YEAR(departure_date) year, MONTH(departure_date) month'),
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->get()
            ->map(function ($item) {
                $orders = Order::with([
                    'packs',
                    'payments',
                    // 'declarations',
                    // 'truck',
                    // 'customer',
                ])
                    // ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                if (isSeller()) {
                    $orders = Order::with([
                        'packs',
                        'payments',
                    ])
                        // ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();
                }

                $trucks = Truck::whereYear('departure_date', $item->year)
                    ->whereMonth('departure_date', $item->month)
                    ->get();

                $revenue = $orders->sum('revenue');
                $revenue = round($revenue);

                $cost = $trucks->sum('total_cost');
                $cost = round($cost);

                $debt = $orders->sum('debt');
                $debt = round($debt);

                $totalTrucks = $trucks->count();
                $totalCustomers = Customer::whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->count();
                $totalOrders = $orders->count();
                $totalOrdersBangTuong = $orders->filter(function ($value) {
                    return $value->location_id === 1;
                })->count();

                $_item['revenue'] = $revenue;
                $_item['cost'] = $cost;
                $_item['debt'] = $debt;
                $_item['year'] = $item->year;
                $_item['month'] = $item->month;
                $_item['total_trucks'] = $totalTrucks;
                $_item['total_customers'] = $totalCustomers;
                $_item['total_orders'] = $totalOrders;
                $_item['total_orders_bang_tuong'] = $totalOrdersBangTuong;

                return $_item;
            });

        // dd($trucksGroupBangTuong);

        $from = $request->get('from');
        $to = $request->get('to');

        $from1 = null;
        $from2 = null;
        $to1 = null;
        $to2 = null;
        $rangeFormatted1 = '';
        $rangeFormatted2 = '';
        $revenue1 = 0;
        $revenue2 = 0;

        if ($from && $to) {
            $splitFrom = explode(' - ', $from);
            $splitTo = explode(' - ', $to);

            $from1 = Carbon::createFromFormat('m/d/Y', $splitFrom[0]);
            $from2 = Carbon::createFromFormat('m/d/Y', $splitFrom[1]);

            $to1 = Carbon::createFromFormat('m/d/Y', $splitTo[0]);
            $to2 = Carbon::createFromFormat('m/d/Y', $splitTo[1]);

            $revenue1 = Order::query()
                ->where('created_at', '>=', $from1)
                ->where('created_at', '<=', $from2)
                ->get()
                ->sum('revenue');

            $revenue2 = Order::query()
                ->where('created_at', '>=', $to1)
                ->where('created_at', '<=', $to2)
                ->get()
                ->sum('revenue');

            // dd(
            //     $from1->format('d-m-Y'),
            //     $from2->format('d-m-Y'),
            //     $to1->format('d-m-Y'),
            //     $to2->format('d-m-Y'),
            //     $revenue1,
            //     $revenue2
            // );

            $rangeFormatted1 = "{$from1->format('m/d/Y')} - {$from2->format('m/d/Y')}";
            $rangeFormatted2 = "{$to1->format('m/d/Y')} - {$to2->format('m/d/Y')}";

            $from1 = $from1->format('d-m-Y');
            $from2 = $from2->format('d-m-Y');
            $to1 = $to1->format('d-m-Y');
            $to2 = $to2->format('d-m-Y');
        }

        $revenueChartFromToData = [
            'from1' => $from1,
            'from2' => $from2,
            'to1' => $to1,
            'to2' => $to2,
            'revenue1' => $revenue1,
            'revenue2' => $revenue2,
            'rangeFormatted1' => $rangeFormatted1,
            'rangeFormatted2' => $rangeFormatted2,
        ];

        return view('home', [
            'totalTrucks' => $totalTrucks,
            'totalCustomers' => $totalCustomers,
            'totalOrders' => $totalOrders,

            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'totalDebt' => $totalDebt,

            'trucksGroup' => $trucksGroup,

            'trucksGroupBangTuong' => $trucksGroupBangTuong,
            'revenueChartFromToData' => $revenueChartFromToData
        ]);
    }

    public function chartOrdersBangTuong(Request $request)
    {
        $totalTrucks = Truck::count();
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();

        $trucksGroup = Truck::select(
            DB::raw('YEAR(departure_date) year, MONTH(departure_date) month'),
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->get()
            ->map(function ($item) {
                $orders = Order::with([
                    'packs',
                    'payments',
                ])
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                $ordersBangTuong = Order::with([
                    'packs',
                    'payments',
                ])
                    ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                $totalCustomersBangTuong = Order::query()
                    ->select(DB::raw('customer_id'))
                    // ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->groupBy('customer_id')
                    ->count();

                if (isSeller()) {
                    $orders = Order::with([
                        'packs',
                        'payments',
                    ])
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();

                    $ordersBangTuong = Order::with([
                        'packs',
                        'payments',
                    ])
                        ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();

                    $totalCustomersBangTuong = Order::query()
                        ->select(DB::raw('customer_id'))
                        ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->groupBy('customer_id')
                        ->count();
                }

                $trucks = Truck::whereYear('departure_date', $item->year)
                    ->whereMonth('departure_date', $item->month)
                    ->get();

                $revenue = $orders->sum('revenue');
                $revenue = round($revenue);

                $cost = $trucks->sum('total_cost');
                $cost = round($cost);

                $debt = $orders->sum('debt');
                $debt = round($debt);

                $totalTrucks = $trucks->count();
                $totalCustomers = Customer::whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->count();
                $totalOrders = $orders->count();
                $totalOrdersBangTuong = $ordersBangTuong->count();

                $_item['revenue'] = $revenue;
                $_item['cost'] = $cost;
                $_item['debt'] = $debt;
                $_item['year'] = $item->year;
                $_item['month'] = $item->month;
                $_item['total_trucks'] = $totalTrucks;
                $_item['total_customers'] = $totalCustomers;
                $_item['total_customers_bang_tuong'] = $totalCustomersBangTuong;
                $_item['total_orders'] = $totalOrders;
                $_item['total_orders_bang_tuong'] = $totalOrdersBangTuong;

                return $_item;
            });

        $months = request('months', 3);
        $totalRevenue = $trucksGroup->take($months)->sum('revenue');
        $totalCost = $trucksGroup->take($months)->sum('cost');
        $totalDebt = $trucksGroup->take($months)->sum('debt');
        $totalOrders = $trucksGroup->take($months)->sum('total_orders');

        $trucksGroupBangTuong = Truck::select(
            DB::raw('YEAR(departure_date) year, MONTH(departure_date) month'),
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->get()
            ->map(function ($item) {
                $orders = Order::with([
                    'packs',
                    'payments',
                    // 'declarations',
                    // 'truck',
                    // 'customer',
                ])
                    // ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                if (isSeller()) {
                    $orders = Order::with([
                        'packs',
                        'payments',
                    ])
                        // ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();
                }

                $trucks = Truck::whereYear('departure_date', $item->year)
                    ->whereMonth('departure_date', $item->month)
                    ->get();

                $revenue = $orders->sum('revenue');
                $revenue = round($revenue);

                $cost = $trucks->sum('total_cost');
                $cost = round($cost);

                $debt = $orders->sum('debt');
                $debt = round($debt);

                $totalTrucks = $trucks->count();
                $totalCustomers = Customer::whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->count();
                $totalOrders = $orders->count();
                $totalOrdersBangTuong = $orders->filter(function ($value) {
                    return $value->location_id === 1;
                })->count();

                $_item['revenue'] = $revenue;
                $_item['cost'] = $cost;
                $_item['debt'] = $debt;
                $_item['year'] = $item->year;
                $_item['month'] = $item->month;
                $_item['total_trucks'] = $totalTrucks;
                $_item['total_customers'] = $totalCustomers;
                $_item['total_orders'] = $totalOrders;
                $_item['total_orders_bang_tuong'] = $totalOrdersBangTuong;

                return $_item;
            });


        $from = $request->get('from');
        $to = $request->get('to');

        $from1 = null;
        $from2 = null;
        $to1 = null;
        $to2 = null;
        $rangeFormatted1 = '';
        $rangeFormatted2 = '';
        $revenue1 = 0;
        $revenue2 = 0;

        if ($from && $to) {
            $splitFrom = explode(' - ', $from);
            $splitTo = explode(' - ', $to);

            $from1 = Carbon::createFromFormat('m/d/Y', $splitFrom[0]);
            $from2 = Carbon::createFromFormat('m/d/Y', $splitFrom[1]);

            $to1 = Carbon::createFromFormat('m/d/Y', $splitTo[0]);
            $to2 = Carbon::createFromFormat('m/d/Y', $splitTo[1]);

            $revenue1 = Order::query()
                ->where('created_at', '>=', $from1)
                ->where('created_at', '<=', $from2)
                ->get()
                ->sum('revenue');

            $revenue2 = Order::query()
                ->where('created_at', '>=', $to1)
                ->where('created_at', '<=', $to2)
                ->get()
                ->sum('revenue');

            // dd(
            //     $from1->format('d-m-Y'),
            //     $from2->format('d-m-Y'),
            //     $to1->format('d-m-Y'),
            //     $to2->format('d-m-Y'),
            //     $revenue1,
            //     $revenue2
            // );

            $rangeFormatted1 = "{$from1->format('m/d/Y')} - {$from2->format('m/d/Y')}";
            $rangeFormatted2 = "{$to1->format('m/d/Y')} - {$to2->format('m/d/Y')}";

            $from1 = $from1->format('d-m-Y');
            $from2 = $from2->format('d-m-Y');
            $to1 = $to1->format('d-m-Y');
            $to2 = $to2->format('d-m-Y');
        }

        $revenueChartFromToData = [
            'from1' => $from1,
            'from2' => $from2,
            'to1' => $to1,
            'to2' => $to2,
            'revenue1' => $revenue1,
            'revenue2' => $revenue2,
            'rangeFormatted1' => $rangeFormatted1,
            'rangeFormatted2' => $rangeFormatted2,
        ];

        return view('app.charts.orders-bang-tuong', [
            'totalTrucks' => $totalTrucks,
            'totalCustomers' => $totalCustomers,
            'totalOrders' => $totalOrders,

            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'totalDebt' => $totalDebt,

            'trucksGroup' => $trucksGroup,

            'trucksGroupBangTuong' => $trucksGroupBangTuong,
            'revenueChartFromToData' => $revenueChartFromToData
        ]);
    }

    public function chartRevenue(Request $request)
    {
        $totalTrucks = Truck::count();
        $totalCustomers = Customer::count();
        $totalOrders = Order::count();

        $trucksGroup = Truck::select(
            DB::raw('YEAR(departure_date) year, MONTH(departure_date) month'),
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->get()
            ->map(function ($item) {
                $orders = Order::with([
                    'packs',
                    'payments',
                ])
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                $ordersBangTuong = Order::with([
                    'packs',
                    'payments',
                ])
                    ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                $totalCustomersBangTuong = Order::query()
                    ->select(DB::raw('customer_id'))
                    ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->groupBy('customer_id')
                    ->count();

                if (isSeller()) {
                    $orders = Order::with([
                        'packs',
                        'payments',
                    ])
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();

                    $ordersBangTuong = Order::with([
                        'packs',
                        'payments',
                    ])
                        ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();

                    $totalCustomersBangTuong = Order::query()
                        ->select(DB::raw('customer_id'))
                        ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->groupBy('customer_id')
                        ->count();
                }

                $trucks = Truck::whereYear('departure_date', $item->year)
                    ->whereMonth('departure_date', $item->month)
                    ->get();

                $revenue = $orders->sum('revenue');
                $revenue = round($revenue);

                $cost = $trucks->sum('total_cost');
                $cost = round($cost);

                $debt = $orders->sum('debt');
                $debt = round($debt);

                $totalTrucks = $trucks->count();
                $totalCustomers = Customer::whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->count();
                $totalOrders = $orders->count();
                $totalOrdersBangTuong = $ordersBangTuong->count();

                $_item['revenue'] = $revenue;
                $_item['cost'] = $cost;
                $_item['debt'] = $debt;
                $_item['year'] = $item->year;
                $_item['month'] = $item->month;
                $_item['total_trucks'] = $totalTrucks;
                $_item['total_customers'] = $totalCustomers;
                $_item['total_customers_bang_tuong'] = $totalCustomersBangTuong;
                $_item['total_orders'] = $totalOrders;
                $_item['total_orders_bang_tuong'] = $totalOrdersBangTuong;

                return $_item;
            });

        $months = request('months', 3);
        $totalRevenue = $trucksGroup->take($months)->sum('revenue');
        $totalCost = $trucksGroup->take($months)->sum('cost');
        $totalDebt = $trucksGroup->take($months)->sum('debt');
        $totalOrders = $trucksGroup->take($months)->sum('total_orders');

        $trucksGroupBangTuong = Truck::select(
            DB::raw('YEAR(departure_date) year, MONTH(departure_date) month'),
        )
            ->groupBy('year', 'month')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->get()
            ->map(function ($item) {
                $orders = Order::with([
                    'packs',
                    'payments',
                    // 'declarations',
                    // 'truck',
                    // 'customer',
                ])
                    // ->where('location_id', 1)
                    ->whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->whereNull('deleted_at')
                    ->get();

                if (isSeller()) {
                    $orders = Order::with([
                        'packs',
                        'payments',
                    ])
                        // ->where('location_id', 1)
                        ->whereYear('created_at', $item->year)
                        ->whereMonth('created_at', $item->month)
                        ->whereNull('deleted_at')
                        ->whereHas('customer.user', function ($q) {
                            $q->where('id', auth()->id());
                        })
                        ->get();
                }

                $trucks = Truck::whereYear('departure_date', $item->year)
                    ->whereMonth('departure_date', $item->month)
                    ->get();

                $revenue = $orders->sum('revenue');
                $revenue = round($revenue);

                $cost = $trucks->sum('total_cost');
                $cost = round($cost);

                $debt = $orders->sum('debt');
                $debt = round($debt);

                $totalTrucks = $trucks->count();
                $totalCustomers = Customer::whereYear('created_at', $item->year)
                    ->whereMonth('created_at', $item->month)
                    ->count();
                $totalOrders = $orders->count();
                $totalOrdersBangTuong = $orders->filter(function ($value) {
                    return $value->location_id === 1;
                })->count();

                $_item['revenue'] = $revenue;
                $_item['cost'] = $cost;
                $_item['debt'] = $debt;
                $_item['year'] = $item->year;
                $_item['month'] = $item->month;
                $_item['total_trucks'] = $totalTrucks;
                $_item['total_customers'] = $totalCustomers;
                $_item['total_orders'] = $totalOrders;
                $_item['total_orders_bang_tuong'] = $totalOrdersBangTuong;

                return $_item;
            });


        $from = $request->get('from');
        $to = $request->get('to');

        $from1 = null;
        $from2 = null;
        $to1 = null;
        $to2 = null;
        $rangeFormatted1 = '';
        $rangeFormatted2 = '';
        $revenue1 = 0;
        $revenue2 = 0;

        if ($from && $to) {
            $splitFrom = explode(' - ', $from);
            $splitTo = explode(' - ', $to);

            $from1 = Carbon::createFromFormat('m/d/Y', $splitFrom[0]);
            $from2 = Carbon::createFromFormat('m/d/Y', $splitFrom[1]);

            $to1 = Carbon::createFromFormat('m/d/Y', $splitTo[0]);
            $to2 = Carbon::createFromFormat('m/d/Y', $splitTo[1]);

            $revenue1 = Order::query()
                ->where('created_at', '>=', $from1)
                ->where('created_at', '<=', $from2)
                ->get()
                ->sum('revenue');

            $revenue2 = Order::query()
                ->where('created_at', '>=', $to1)
                ->where('created_at', '<=', $to2)
                ->get()
                ->sum('revenue');

            $rangeFormatted1 = "{$from1->format('m/d/Y')} - {$from2->format('m/d/Y')}";
            $rangeFormatted2 = "{$to1->format('m/d/Y')} - {$to2->format('m/d/Y')}";

            $from1 = $from1->format('d-m-Y');
            $from2 = $from2->format('d-m-Y');
            $to1 = $to1->format('d-m-Y');
            $to2 = $to2->format('d-m-Y');
        }

        $revenueChartFromToData = [
            'from1' => $from1,
            'from2' => $from2,
            'to1' => $to1,
            'to2' => $to2,
            'revenue1' => $revenue1,
            'revenue2' => $revenue2,
            'rangeFormatted1' => $rangeFormatted1,
            'rangeFormatted2' => $rangeFormatted2,
        ];

        return view('app.charts.revenue', [
            'totalTrucks' => $totalTrucks,
            'totalCustomers' => $totalCustomers,
            'totalOrders' => $totalOrders,

            'totalRevenue' => $totalRevenue,
            'totalCost' => $totalCost,
            'totalDebt' => $totalDebt,

            'trucksGroup' => $trucksGroup,

            'trucksGroupBangTuong' => $trucksGroupBangTuong,
            'revenueChartFromToData' => $revenueChartFromToData
        ]);
    }
}
