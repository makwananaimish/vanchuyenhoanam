<?php

namespace App\Http\Controllers\App;

use App\TopCustomer;
use App\TopSeller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class TopRevenueController extends BaseController
{
    private function getTopHunter($month, $year, $top = 5)
    {
        $selectedMonth = $year . '-' . $month;

        $startDate = $selectedMonth . '-01';
        $endDate = date('Y-m-t', strtotime($selectedMonth));

        $sellers = User::query()
            ->where('email', '!=', 'phuongdang29492@gmail.com')
            ->with([
                'customers' => function ($query) use ($startDate, $endDate) {
                    return $query->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate);
                }
            ])
            ->whereHas(
                'customers',
                function ($query) use ($startDate, $endDate) {
                    return $query->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate);
                }
            )
            ->get()
            ->map(function ($seller) {
                $seller->revenue =
                    $seller->customers->sum('revenue');

                return $seller;
            })
            ->sortByDesc('revenue')
            ->take($top)
            ->all();

        return $sellers;
    }

    private function getTopSale($month, $year, $top = 10)
    {
        $selectedMonth = $year . '-' . $month;

        $startDate = $selectedMonth . '-01';
        $endDate = date('Y-m-t', strtotime($selectedMonth));

        $sellers = User::query()
            ->where('email', '!=', 'phuongdang29492@gmail.com')
            ->with([
                'customers.orders.payments' => function ($query) use ($startDate, $endDate) {
                    return $query->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate);
                }
            ])
            ->whereHas(
                'customers.orders.payments',
                function ($query) use ($startDate, $endDate) {
                    return $query->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate);
                }
            )
            ->get()
            ->map(function ($seller) {
                $seller->revenue =
                    $seller->customers->sum(function ($customer) {
                        return $customer->orders->sum(function ($order) {
                            return $order->payments->sum('amount');
                        });
                    });

                return $seller;
            })
            ->sortByDesc('revenue')
            ->take($top)
            ->all();

        return $sellers;
    }

    public function index(Request $request)
    {
        $month = Carbon::createFromFormat('Y-m', $request->get('month') ?? Carbon::now()->format('Y-m'));

        $hunters = $this->getTopHunter($month->get('month'), $month->get('year'));
        $topSale = $this->getTopSale($month->get('month'), $month->get('year'));

        return view('app.top_revenue.index', [
            'hunters' => $hunters,
            'topSale' => $topSale,
        ]);
    }

    public function getCustomers(Request $request, $hunterId)
    {
        // $month = Carbon::createFromFormat('Y-m', $request->get('month', Carbon::now()->format('Y-m')));
        $month = Carbon::createFromFormat('Y-m', $request->get('month') ?? Carbon::now()->format('Y-m'));

        $hunters = $this->getTopHunter($month->get('month'), $month->get('year'));
        $topSale = $this->getTopSale($month->get('month'), $month->get('year'));

        // dd($topSale);

        $topSellers = TopSeller::with(['user'])
            ->where('month', $month->get('month'))
            ->where('year', $month->get('year'))
            ->orderBy('commission', 'DESC')
            ->limit(10)
            ->get();

        $topCustomers = TopCustomer::with(['customer'])
            ->where('month', $month->get('month'))
            ->where('year', $month->get('year'))
            ->orderBy('revenue', 'DESC')
            ->limit(5)
            ->get();

        // dd(
        //     $hunterId,
        //     $hunters
        // );

        return view('app.top_revenue.customers', [
            'hunters' => $hunters,
            'hunterId' => $hunterId,
            'topSale' => $topSale,
            'topSellers' => $topSellers,
            'topCustomers' => $topCustomers,
        ]);
    }
}
