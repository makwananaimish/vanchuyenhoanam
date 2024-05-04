<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $code = $request->get('code');

        $query = Order::query();

        if ($code) {
            $query = $query->where('code', 'LIKE', "%$code%");
        }

        $orders = $query->limit(15)->get([
            'id',
            'code'
        ]);

        return response()->json($orders);
    }
}
