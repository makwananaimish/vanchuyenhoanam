<?php

namespace App\Http\Controllers\App;

use App\Customer;
use App\Location;
use App\NotifyAddress;
use App\Order;
use App\Pack;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CustomerController extends BaseController
{
    public function index(Request $request)
    {
        $customerSortByRevenue = json_decode(Cache::get('customer:sort-by-revenue'));
        $ids = collect($customerSortByRevenue)->map(function ($item, $key) {
            return $item->id;
        })->values()->all();

        $options = $request->all();

        if (!auth()->user()->is_admin) {
            $options['user_id'] = auth()->id();
        }

        $role = auth()->user()->role;

        if ($role === User::ROLE_ACCOUNTANT) {
            $options['user_id'] = null;
        }

        $query = Customer::with([
            'orders.packs',
            'orders.payments',
        ])
            // ->whereDoesntHave('orders')
            // ->where('id', '<', 20)
        ;

        $paginateQuery = Customer::with([
            'orders.packs',
            'orders.payments',
        ])
            // ->where('id', '<', 20)
        ;

        $id = Arr::get($options, 'id');
        $userId = Arr::get($options, 'user_id');
        $q = Arr::get($options, 'q');
        $hasUser = Arr::get($options, 'has_user');
        $page = Arr::get($options, 'page', 1);
        $sortType = Arr::get($options, 'sort_type');
        $debtDesc = Arr::get($options, 'debt_desc');

        if ($debtDesc == 1) {
            $sortType = 'debt_desc';
        }

        if ($id) {
            $query = $query
                ->where('id', $id);

            $paginateQuery = $paginateQuery
                ->where('id', $id);
        }

        if ($userId) {
            $query = $query
                ->where('user_id', $userId);

            $paginateQuery = $paginateQuery
                ->where('user_id', $userId);
        }

        if ($q) {
            $query = $query
                ->where(
                    'name',
                    'LIKE',
                    "%$q%"
                )
                ->orWhere(
                    'code',
                    'LIKE',
                    "%$q%"
                )
                ->orWhere('phone', 'LIKE', "%$q%");
            $paginateQuery = $paginateQuery
                ->where(
                    'name',
                    'LIKE',
                    "%$q%"
                )
                ->orWhere(
                    'code',
                    'LIKE',
                    "%$q%"
                )
                ->orWhere('phone', 'LIKE', "%$q%");
        }

        if ($hasUser == '0') {
            $query = $query
                ->doesntHave('user');
            $paginateQuery = $paginateQuery
                ->doesntHave('user');
        }

        if ($sortType) {


            $months = 0;

            if ($sortType === 'not_use_1_month') {
                $months = 1;
            }
            if ($sortType === 'not_use_2_month') {
                $months = 2;
            }
            if ($sortType === 'not_use_3_month') {
                $months = 3;
            }



            if ($sortType === 'not_use_1_month' || $sortType === 'not_use_2_month' || $sortType === 'not_use_3_month') {
                // dd($months);

                $query = $query
                    ->whereDoesntHave('orders', function ($query) use ($months) {
                        // $query->where('created_at', '>=', Carbon::now()->subMonths($months));
                        $query->whereDate('orders.created_at', '>=', Carbon::now()->subDays($months  * 30));
                    })->whereHas('orders', function ($query) use ($months) {
                        // $query->where('created_at', '<', Carbon::now()->subMonths($months));
                        $query
                            ->whereDate('orders.created_at', '<', Carbon::now()->subDays($months  * 30))
                            ->whereDate('orders.created_at', '>', Carbon::now()->subDays(($months + 1)  * 30));
                    });

                // dd($query->toSql());

                $paginateQuery = $paginateQuery
                    ->whereDoesntHave('orders', function ($query) use ($months) {
                        // $query->where('created_at', '>=', Carbon::now()->subMonths($months));
                        $query->whereDate('created_at', '>=', Carbon::now()->subDays($months  * 30));
                    })
                    ->whereHas('orders', function ($query) use ($months) {
                        // $query->where('created_at', '<', Carbon::now()->subMonths($months));
                        $query
                            ->whereDate('created_at', '<', Carbon::now()->subDays($months  * 30))
                            ->whereDate('created_at', '>', Carbon::now()->subDays(($months + 1)  * 30));
                    });
            }
        }


        $query = $query->orderByRaw(DB::raw("FIELD(customers.id, " . implode(",", $ids) . ")"));

        $paginate = $paginateQuery->paginate();

        $customers = $query
            ->get();

        if ($sortType) {
            if ($sortType === 'debt_desc') {
                $customers = $customers->sortByDesc('debt');
            }
        }

        $customers = $customers->skip(($page - 1) * 15)
            ->take(15);

        $allCustomers = Customer::orderBy('id', 'DESC')
            ->get();

        $_customers = Customer::with([
            'orders.packs',
            'orders.payments',
        ])->get();

        if ($role === User::ROLE_SELLER) {
            $totalDebt = Customer::with([
                'orders.packs',
                'orders.payments',
            ])
                ->where('user_id', auth()->id())
                ->get()
                ->sum('debt');

            $totalBalance = Customer::with([
                'orders.packs',
                'orders.payments',
            ])
                ->where('user_id', auth()->id())
                ->get()
                ->sum('balance');
        } else {
            $totalDebt = $_customers->sum('debt');
            $totalBalance = $_customers->sum('balance');
        }

        return view('app.customer.index', [
            'paginate' => $paginate,
            'allCustomers' => $allCustomers,
            'customers' => $customers,
            'totalDebt' => $totalDebt,
            'totalBalance' => $totalBalance,
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('has-permissions', 'create.customer');

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers')
            ],
            'phone' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
            ],
        ]);

        $redirect = $data['redirect'];

        if ($validator->fails()) {
            return redirect()
                ->to("$redirect#create-customer" ?? 'customers#create-customer')
                ->withErrors($validator)
                ->withInput();
        }

        $data['password'] = Hash::make($data['password']);

        Customer::create($data);

        return $redirect ? redirect()->to($redirect) : redirect()->route('customer.index');
    }

    public function show(Customer $customer, Request $request)
    {
        $statusText = request('status_text');
        $deliveredStatusText = request('delivered_status_text');
        $id = request('id');
        $locationId = $request->get('current_location_id');

        $customer->loadMissing([
            'orders' => function ($query) use ($locationId) {
                $_query = $query->with([
                    'truck.currentLocation',
                    'packs',
                    'payments.transaction',
                    'declarations',
                    'truck',
                    'customer',
                    'transactions',
                    'messages.messageViews',
                ]);

                if ($locationId) {
                    $_query = $_query->orWhereHas('truck.currentLocation', function ($q) use ($locationId) {
                        $q->where('id', $locationId);
                    });
                }
            },
            'user'
        ]);

        // $allOrders = Order::with([
        //     'truck.currentLocation',
        //     'packs',
        //     'payments.transaction',
        //     'declarations',
        //     'truck',
        //     'customer',
        //     'transactions',
        //     'messages.messageViews'
        // ]);

        // if ($locationId) {
        //     $allOrders = $allOrders
        //         ->orWhereHas('truck.currentLocation', function ($q) use ($locationId) {
        //             $q->where('id', $locationId);
        //         });
        // }

        // $allOrders = $allOrders->get();

        $query = Order::with([
            'truck.currentLocation',
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
            'messages.messageViews'
        ]);
        $orders = $query
            ->where('customer_id', $customer->id)
            ->get();

        $orders = $orders->filter(function ($order) use ($statusText) {
            if ($statusText) {
                return $order->status_text === $statusText;
            }

            return
                $order->status_text !== Order::STATUS_TEXT_WAIT_FOR_PAYING &&
                $order->status_text !== Order::STATUS_TEXT_IS_NOT_CALCULATED_COST &&
                $order->status_text !== Order::STATUS_TEXT_COMPLETED;
        });

        $noNameOrders = Order::with([
            'truck.currentLocation',
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
            'messages.messageViews'
        ])->whereHas('customer', function ($q) {
            $q->where('code', Customer::NONAME_CODE);
        })->get();

        $expressOrders = Order::with([
            'truck.currentLocation',
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
            'messages.messageViews'
        ])->whereHas('customer', function ($q) {
            $q->where('code', Customer::EXPRESS_CODE);
        })->get();

        $deliveredOrders = $customer->orders
            ->where('status', Pack::DELIVERED)
            ->where('status_text', '!=', Order::STATUS_TEXT_COMPLETED);
        if (!is_null($deliveredStatusText)) {
            $deliveredOrders = $deliveredOrders->where('status_text', $deliveredStatusText);
        }
        $deliveredOrders = $deliveredOrders->sortByDesc('delivery_date');
        // $deliveredOrders = [];

        $completeOrders = $customer->orders->where('status_text', Order::STATUS_TEXT_COMPLETED)->sortByDesc('delivery_date');
        // $completeOrders = [];

        if (
            $statusText === Order::STATUS_TEXT_UNDELIVERED ||
            $statusText === Order::STATUS_TEXT_COMPLETED ||
            $statusText === Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA
        ) {
            $customer->orders = $customer->orders->filter(function ($order) use ($statusText) {
                return $order->status_text === $statusText;
            });
        } elseif ($statusText === Order::STATUS_TEXT_IN_VIETNAM) {
            $customer->orders = $customer->orders->filter(function ($order) {
                return optional(optional(optional($order)->truck)->currentLocation)->name === Location::VIETNAM_INVENTORY && $order->status_text === Order::STATUS_TEXT_UNDELIVERED;
            });
        } elseif ($statusText === Order::STATUS_TEXT_DELIVERED) {
            $customer->orders = $customer->orders->filter(function ($order) {
                return  $order->status === Pack::DELIVERED;
            });
        } elseif ($statusText === Order::STATUS_TEXT_NONAME) {
            $customer->orders = $customer->orders->filter(function ($order) {
                return  $order->customer->code === Customer::NONAME_CODE;
            });
        }

        if ($id) {
            $customer->orders = $customer->orders->filter(function ($order) use ($id) {
                return $order->id === $id;
            });
        }

        $locations = Location::all();
        $customers = Customer::all();
        $users = User::all()->filter(function ($user) {
            return !$user->is_admin;
        });
        // $debt = Order::where('customer_id', $customer->id)->get()->filter(function ($order) {
        //     return $order->is_calculated_cost;
        // })->sum('debt');

        // $debt = DB::table('view_orders')
        //     ->whereRaw("customer_id = {$customer->id} AND ( taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)")
        //     ->sum('debt');
        $debt = Order::with([
            'packs',
            'payments',
        ])
            ->whereRaw("customer_id = {$customer->id} AND ( taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0)")
            ->get()
            ->sum('debt');

        $debtOrders = Order::with([
            'truck.currentLocation',
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
            'messages.messageViews'
        ])->where('customer_id', $customer->id)
            ->get()
            ->filter(function ($order) {
                return $order->debt > 1 && $order->status_text !== Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA;
            });

        return view('app.customer.show', [
            'customer' => $customer,
            'customers' => $customers,
            'locations' => $locations,
            'orders' => $orders,
            'deliveredOrders' => $deliveredOrders,
            'completeOrders' => $completeOrders,
            'noNameOrders' => $noNameOrders,
            'expressOrders' => $expressOrders,
            'users' => $users,
            'debt' => $debt,
            'debtOrders' => $debtOrders,
        ]);
    }

    public function showOrders(Customer $customer, Request $request)
    {
        $customerCode = $request->get('customer_code');
        $declaration = $request->get('declaration');
        $id = $request->get('id');

        $customer->loadMissing([
            'user'
        ]);

        $query = Order::with([
            'truck.currentLocation',
            'payments',
            'packs.order',
            'payments.transaction',
            'declarations',
            'truck',
            'customer',
            'messages.messageViews',
        ]);

        if (!is_null($declaration)) {
            $query = $query->whereHas('declarations');
        }

        if ($customerCode) {
            $orders = $query
                ->whereHas('customer', function ($q) use ($customerCode) {
                    $q->where('code', $customerCode);
                })
                ->orderBy('created_at', 'DESC')
                ->get();

            $waitForPayingOrders = [];
            $completedOrders = [];
            $undeliveredOrders = [];
            $isReceivedInChinaOrders = [];

            if ($customerCode === Customer::NONAME_CODE) {
                return view('app.customer.order.noname', [
                    'customer' => $customer,
                    'orders' => $orders,
                ]);
            } elseif ($customerCode === Customer::EXPRESS_CODE) {
                return view('app.customer.order.express', [
                    'customer' => $customer,
                    'orders' => $orders,
                ]);
            }
        } else {
            if (!is_null($id)) {
                $query = $query->where('id', $id);
            }

            $orders = $query
                ->where('customer_id', $customer->id)
                ->get();
            $_orders = $orders->filter(function ($order) {
                return $order->status_text !== Order::STATUS_TEXT_COMPLETED;
            });

            $waitForPayingOrders = $orders->filter(function ($order) {
                return $order->status_text === Order::STATUS_TEXT_WAIT_FOR_PAYING ||
                    $order->status_text === Order::STATUS_TEXT_IS_NOT_CALCULATED_COST;
            })->sortBy('status_text');

            $completedOrders = $orders->filter(function ($order) {
                return $order->status_text === Order::STATUS_TEXT_COMPLETED;
            });

            $undeliveredOrders = $orders->filter(function ($order) {
                return $order->status_text === Order::STATUS_TEXT_UNDELIVERED;
            });

            $isReceivedInChinaOrders = $orders->filter(function ($order) {
                return $order->status_text === Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA;
            });

            $orders = $orders->filter(function ($order) {
                return $order->status_text !== Order::STATUS_TEXT_COMPLETED;
            });
        }

        $debtOrders = Order::with([
            'truck.currentLocation',
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
            'messages.messageViews'
        ])->where('customer_id', $customer->id)
            ->get()
            ->filter(function ($order) {
                return $order->debt > 1;
            });

        return view('app.customer.order.index', [
            'customer' => $customer,
            'orders' => $_orders,
            'waitForPayingOrders' => $waitForPayingOrders,
            'completedOrders' => $completedOrders,
            'undeliveredOrders' => $undeliveredOrders,
            'isReceivedInChinaOrders' => $isReceivedInChinaOrders,
            'debtOrders' => $debtOrders,
        ]);
    }

    public function showCompletedOrders(Customer $customer, Request $request)
    {
        $declaration = $request->get('declaration');
        $id = $request->get('id');
        $month = request('month');

        $customer->loadMissing([
            'user'
        ]);

        $query = Order::with([
            'truck.currentLocation',
            'payments',
            'packs.order',
            'payments.transaction',
            'declarations',
            'truck',
            'customer',
            'messages.messageViews',
        ]);

        if (!is_null($declaration)) {
            $query = $query->whereHas('declarations');
        }

        if (!is_null($id)) {
            $query = $query->where('id', $id);
        }

        if (!is_null($month)) {
            $_month = Carbon::createFromFormat('Y-m', $month);
            $query = $query
                ->whereMonth('created_at', '>=', $_month->month)
                ->whereYear('created_at', '>=', $_month->year);
        }

        $orders = $query
            ->where('customer_id', $customer->id)
            ->get();

        $completedOrders = $orders->filter(function ($order) {
            return $order->status_text === Order::STATUS_TEXT_COMPLETED;
        });

        return view('app.customer.order.completed', [
            'customer' => $customer,
            'orders' => $orders,
            'completedOrders' => $completedOrders,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        DB::beginTransaction();

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('customers')->ignore($customer->id, 'id')
            ],
            'phone' => [
                'required',
                'string',
                'max:255',
            ],
            'password' => [
                'required',
                'string',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('customers/' .  $customer->id . '#update-customer')
                ->withErrors($validator)
                ->withInput();
        }

        $data['password'] = Hash::make($data['password']);
        $customer->fill($data);
        $customer->save();

        // foreach ($customer->getChanges() as $attribute => $value) {
        //     if ($attribute !== 'updated_at') {
        //         Gate::authorize('has-permissions', 'update.customer.' . $attribute);
        //     }
        // }

        DB::commit();

        return redirect()
            ->route('customer.show', [
                'customer' => $customer
            ]);
    }

    public function updateUser(Request $request, Customer $customer)
    {
        Gate::authorize('only-admin');

        $data = $request->all();
        $customer->user_id = $data['user_id'];
        $customer->save();

        return redirect()
            ->back();
    }

    public function destroy(Customer $customer)
    {
        Gate::authorize('only-admin');

        if ($customer->code !== Customer::NONAME_CODE) {
            $noName = Customer::where('code', Customer::NONAME_CODE)->first();

            if ($noName) {
                Order::where('customer_id', $customer->id)->update([
                    'customer_id' => $noName->id
                ]);
            }

            $customer->delete();
        }

        return redirect()->back();
    }

    public function notifyAddress(Customer $customer, Request $request)
    {
        $data = $request->all();

        $ids = explode(',', $data['ids']);

        foreach ($ids as $key => $id) {
            NotifyAddress::query()
                ->updateOrCreate([
                    'order_id' => $id
                ], [
                    'date' => $data['date'],
                    'address' => $data['address'],
                    'phone' => $data['phone'],
                    'name' => $data['name'],
                    'note' => $data['note'],
                ]);
        }

        return redirect()->back();
    }
}
