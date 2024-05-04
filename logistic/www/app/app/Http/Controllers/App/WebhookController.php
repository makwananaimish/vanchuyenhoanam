<?php

namespace App\Http\Controllers\App;

use App\BankTransaction;
use App\Http\Controllers\Controller;
use App\Location;
use App\Option;
use App\Order;
use App\Services\TCB;
use App\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function listenAndroid(Request $request, TCB $tcb)
    {
        try {
            $data = $request->all();

            $transaction = $tcb->getTransactionFromAndroid($data);

            if ($transaction['amount'] > 0) {
                BankTransaction::firstOrCreate([
                    'bank' => BankTransaction::TECHCOMBANK,
                    'content' => $transaction['content'],
                    'amount' => $transaction['amount'],
                    // 'date' => $transaction['format_date'],
                ], [
                    'bank' => BankTransaction::TECHCOMBANK,
                    'content' => $transaction['content'],
                    'amount' => $transaction['amount'],
                    'date' => $transaction['format_date'],
                ]);
            }

            Log::info($data);

            $transactions = [$transaction];

            $tcb->processV2($transactions);

            return response()->json([
                'status' => true
            ]);
        } catch (\Throwable $th) {
            Log::error('Listen android error:');
            Log::error($th->getMessage());

            return response()->json([
                'status' => 'error',
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getWhitelistAccountNumbers(Request $request)
    {
        try {
            $data = $request->all();

            Log::info($data);

            $whitelistAccountNumbers = optional(Option::where('name', 'whitelist_account_numbers')->first())->value;
            $separator = "/\s+/";
            $result = preg_split($separator, $whitelistAccountNumbers);
            $result = array_map('trim', $result);

            return response()->json([
                'status' => true,
                'data' => $result
            ]);
        } catch (\Throwable $th) {
            Log::error('Listen android error:');
            Log::error($th->getMessage());

            return response()->json([
                'status' => false,
                'error' => $th->getMessage()
            ]);
        }
    }

    public function getBankTransactions()
    {
        $apiToken = optional(Option::where('name', 'api_token')->first())->value;
        $apiWhitelistIp = optional(Option::where('name', 'api_whitelist_ip')->first())->value;

        $ip = request()->ip();
        $token = request()->header('token');
        $ips = preg_split('/\r\n|\r/', trim($apiWhitelistIp));
        $records = (int) request()->get('records', 100);
        $remoteIp = request()->header('CF-Connecting-IP');

        Log::info($ip);

        if ($records > 100) {
            $records = 100;
        }

        if ($records < 15) {
            $records = 15;
        }

        if ($token !== $apiToken) {
            return response()->json([
                'status' => 'error',
                'error' => 'Token invalid'
            ]);
        }

        // if (!in_array($remoteIp, $ips)) {
        //     return response()->json([
        //         'status' => 'error',
        //         'error' => 'Ip not in whitelist',
        //         'ip' => $ip,
        //         'remote_ip' => $remoteIp
        //     ]);
        // }

        $transactions = BankTransaction::where('bank', BankTransaction::TECHCOMBANK)
            ->where('content', 'LIKE', "%19038769553019%")
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->take($records)
            ->get();

        $time = Carbon::now()->format('Y-m-d H:i:s');

        return response()->json([
            'status' => 'ok',
            'hash' => hash('sha256', $token . $time),
            'time' => $time,
            'data' => $transactions,
        ]);
    }

    private function field($arr, $fields = [])
    {
        if (count($fields) === 0) {
            return $arr;
        }

        if (!in_array('customer_id', $fields)) {
            unset($arr['customer_id']);
        }

        if (!in_array('name', $fields)) {
            unset($arr['name']);
        }

        if (!in_array('phone', $fields)) {
            unset($arr['phone']);
        }

        if (!in_array('money_estimated', $fields)) {
            unset($arr['money_estimated']);
        }

        if (!in_array('to_address', $fields)) {
            unset($arr['to_address']);
        }

        if (!in_array('final_amount', $fields)) {
            unset($arr['final_amount']);
        }

        if (!in_array('balance', $fields)) {
            unset($arr['balance']);
        }

        if (!in_array('unpaid_debt', $fields)) {
            unset($arr['unpaid_debt']);
        }

        return $arr;
    }

    public function getCustomers()
    {
        $fields = request()->get('fields');

        $fields = $fields === null ? [] : explode(',', $fields);

        // dd($fields);

        $trucks = Truck::query()
            ->whereHas('currentLocation', function ($q) {
                $q->name = Location::VIETNAM_INVENTORY_2;
            })
            ->orderBy('id', 'DESC')
            ->limit(30)
            ->get();

        // dd($trucks);

        $resp = [];

        foreach ($trucks as $key => $truck) {
            $orders = $truck->orders;

            $customers = [];

            foreach ($orders as $key => $order) {
                Log::info("order #" . $order->id);
                Log::info("customer_id #" . $order->customer_id);

                $customer = Arr::get($customers, $order->customer_id);
                $customerObj = $order->customer;

                if ($customer) {
                    $customers[$order->customer_id]['money_estimated'] += $order->debt;
                    $customers[$order->customer_id]['final_amount'] += $order->debt;
                    $customers[$order->customer_id]['orders'][] = [
                        'id' => $order->id,
                        'status' => $order->status_text
                    ];
                } else {
                    $query = Order::with([
                        'truck.currentLocation',
                        'payments',
                        'packs.order.truck.currentLocation',
                        'declarations',
                        'truck',
                        'customer',
                    ]);

                    $debt =
                        $query
                        ->where('customer_id', $customerObj->id)
                        ->orderBy('created_at')
                        ->get()
                        ->filter(function ($order) {
                            return $order->status_text === Order::STATUS_TEXT_WAIT_FOR_PAYING;
                        })
                        ->sum(function ($order) {
                            return round($order->debt);
                        });

                    $customers[$order->customer_id] = [
                        'customer_id' => $order->customer_id,
                        'name' => $customerObj->name,
                        'phone' => $customerObj->phone,
                        'money_estimated' => $order->fare,
                        'to_address' => $customerObj->address,
                        'final_amount' => $order->debt,
                        'balance' => $customerObj->balance,
                        'unpaid_debt' => $debt,
                        'orders' => [
                            [
                                'id' => $order->id,
                                'status' => $order->status_text,
                            ]
                        ]
                    ];
                }
            }

            foreach ($customers as $key => $customer) {
                $customers[$key] = $this->field($customers[$key], $fields);
            }

            $resp[$truck->id] = $customers;
        }

        return response()->json($resp);
    }
}
