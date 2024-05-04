<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class TestController extends Controller
{
    public function index(Request $request)
    {
        $customerSortByRevenue = json_decode(Cache::get('customer:sort-by-revenue'));
        $ids = collect($customerSortByRevenue)->map(function ($item, $key) {
            return $item->id;
        })->values()->all();

        dd($ids);

        return response()->json($ids);
    }
}
