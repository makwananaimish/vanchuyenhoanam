<?php

namespace App\Http\Controllers\App;

use App\Customer;
use App\Order;
use App\Payment;
use App\Repositories\CustomerRepository;
use App\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PayableController extends BaseController
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        parent::__construct();

        $this->customerRepository = $customerRepository;
    }

    public function getOrders(Request $request)
    {
        Gate::authorize('only-customer');

        $customer = CustomerRepository::user();

        $customer = Customer::find($customer->id);

        $customer->loadMissing([
            'user'
        ]);

        $query = Order::with([
            'truck.currentLocation',
            'payments',
            'packs.order.truck.currentLocation',
            'declarations',
            'truck',
            'customer',
        ]);

        $waitForPayingOrders = $query
            ->where('customer_id', $customer->id)
            ->orderBy('created_at')
            ->get()
            ->filter(function ($order) {
                return $order->status_text === Order::STATUS_TEXT_WAIT_FOR_PAYING;
            });

        return view('app.payable.index', [
            'customer' => $customer,
            'waitForPayingOrders' => $waitForPayingOrders,
        ]);
    }

    public function pay(Request $request)
    {
        Gate::authorize('only-customer');

        DB::beginTransaction();

        $customer = $this->customerRepository->getAuthUserAndLock();

        $data = $request->all();

        $validator = Validator::make($data, [
            'order_ids' => [
                'required',
                'array'
            ],
            'order_ids.*' => [
                'integer',
                Rule::exists('orders', 'id')->where('customer_id', $customer->id)
            ],
        ]);

        if ($validator->fails()) {
            return response()
                ->json([
                    'success' => false,
                    'error' => $validator->errors()->first()
                ]);
        }

        $orders = Order::whereIn('id', $data['order_ids'])->get();

        $debt = $orders->sum(function ($order) {
            return round($order->debt);
        });

        $ids = collect($data['order_ids'])->join(', ');

        if ($debt > $customer->balance) return response()
            ->json([
                'success' => false,
                'debt' => $debt,
                'error' => 'Không đủ số dư, nạp tiền trước đã ạ !!!',
            ]);

        $balance = $customer->balance - $debt;

        $transaction =  Transaction::create([
            'type' => Transaction::TYPE_PAYMENT,
            'customer_id' => $customer->id,
            'amount' => $debt,
            'balance' => $balance,
            'status' => Transaction::STATUS_TEXT_COMPLETED,
            'description' => "Pay orders $ids"
        ]);

        $this->customerRepository->updateBalance($customer->id, $balance);

        foreach ($orders as $order) {
            Payment::create([
                'order_id' => $order->id,
                'transaction_id' => $transaction->id,
                'amount' => round($order->debt),
                'image' => '1.jpg'
            ]);
        }

        DB::commit();

        return response()
            ->json([
                'success' => true,
                'debt' => $debt
            ]);
    }

    public function getDebt(Request $request)
    {
        Gate::authorize('only-customer');

        $customer = $this->customerRepository->getAuthUserAndLock();

        $data = $request->all();

        $validator = Validator::make($data, [
            'order_ids' => [
                'required',
                'array'
            ],
            'order_ids.*' => [
                'integer',
                Rule::exists('orders', 'id')->where('customer_id', $customer->id)
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'debt' => 0,
                'error' => $validator->errors()->first()
            ]);
        }

        $debt = Order::with([
            'truck.currentLocation',
            'payments',
            'packs',
            'declarations',
            'truck',
            'customer',
        ])
            ->whereIn('id', $data['order_ids'])
            ->get()
            ->sum(function ($order) {
                return round($order->debt);
            });

        return response()->json([
            'success' => true,
            'debt' => $debt,
        ]);
    }
}
