<?php

namespace App\Http\Controllers\App;

use App\Customer;
use App\Location;
use App\NotifyAddress;
use App\Option;
use App\Order;
use App\Pack;
use App\Services\Uploader;
use App\Services\Webhook;
use App\Truck;
use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class OrderController extends BaseController
{
    public function index(Request $request)
    {
        // dd(Order::with(['customer'])
        //     ->get());

        // // createViewOrders();

        // // dd(DB::table('view_orders2')->get());
        // // DB::table('view_orders2')->paginate();
        // // DB::table('view_orders2')->get();

        // $orders = Order::query()
        //     // ->selectRaw('COUNT(payments.id)')
        //     ->select('payments.*')

        //     ->leftJoin('payments', 'orders.id', 'payments.order_id')
        //     ->where('orders.id', 3)
        //     ->whereNull('payments.deleted_at')
        //     ->leftJoin('packs', 'orders.id', 'packs.order_id')
        //     ->get();

        // dd($orders);


        $query = Order::with([
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
        ]);

        $name = $request->get('name');
        $id = $request->get('id');
        $bill = $request->get('bill');
        $customerCode = $request->get('customer_code');
        $productName = $request->get('product_name');
        $month = $request->get('month');
        $type = $request->get('type');
        $status = $request->get('status');
        $statusText = $request->get('status_text');
        $perPage = $request->get('per_page', 50);

        $selectIsCalculatedCost = "taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0";
        $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
        $selectFareByWeight = '( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) )';
        $selectFareByCubicMeters = '( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) )';
        $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
        $selectRevenue = "IFNULL($selectFare, 0) + IFNULL(taxes, 0) + IFNULL($selectOtherCosts, 0)";
        $selectPaid = 'IFNULL((SELECT SUM( IFNULL(payments.amount, 0) ) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL LIMIT 1), 0)';
        $selectDebt = "IFNULL($selectRevenue, 0) - IFNULL($selectPaid, 0)";


        $selectPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ";
        $selectDeliveredPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id AND packs.status = " . Pack::DELIVERED;

        $selectStatus = "
            IF( 
                ($selectPacksCount) = 0,

                " . Pack::IN_PROGRESS . ",

                IF(
                    ($selectPacksCount) = ($selectDeliveredPacksCount),

                    " . Pack::DELIVERED . ",

                    " . Pack::IN_PROGRESS . "
                )
            )
        ";

        $selectStatusText = "
            IF(
                truck_id IS NULL,

                '" . Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA . "',

                IF(
                    $selectStatus = " . PACK::DELIVERED . ",

                    IF(
                        $selectIsCalculatedCost = 1,

                        IF(
                            $selectDebt = 0,

                            '" . Order::STATUS_TEXT_COMPLETED . "',

                            '" . Order::STATUS_TEXT_WAIT_FOR_PAYING . "'
                        ),

                        '" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "'
                    ),

                    '" . Order::STATUS_TEXT_UNDELIVERED . "'
                )
            )
        ";

        $query = $query

            // ->select()
            // ->addSelect(DB::raw("($selectIsCalculatedCost) AS is_calculated_cost2"))
            // ->addSelect(DB::raw("($selectStatusText) AS status_text2"))
            // ->addSelect(DB::raw("($selectDebt) AS debt2"))
            ->whereHas('customer', function ($q) {
                $q->whereNotIn('code', [Customer::NONAME_CODE, Customer::EXPRESS_CODE]);
            });

        if (!auth()->user()->is_admin) {
            $query = $query->whereHas('customer', function ($q) {
                $q->where('user_id', auth()->id());
            })
                // ->orWhereHas('customer', function ($q) {
                //     $q->whereIn('code', [Customer::NONAME_CODE, Customer::EXPRESS_CODE]);
                // })
            ;
        }

        if ($name) {
            $query = $query->where('name', 'LIKE', "%$name%");
        }

        if ($id) {
            $query = $query->where('id', $id);
        }

        if ($bill) {
            $query = $query->where('bill', 'LIKE', "%$bill%");
        }

        // if ($customerCode) {
        //     $query = $query->whereHas('customer', function ($q) use ($customerCode) {
        //         $q->where('code', 'LIKE', "%$customerCode%");
        //     });
        // } else {
        //     $query = $query->whereHas('customer', function ($q) {
        //         $q->whereNotIn('code', [Customer::NONAME_CODE, Customer::EXPRESS_CODE]);
        //     });
        // }

        if ($productName) {
            $query = $query->where('product_name', 'LIKE', "%$productName%");
        }

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $query = $query
                    ->whereMonth('created_at', $date->format('m'))
                    ->whereYear('created_at', $date->format('Y'));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if ($type) {
            if ($type == 1) {
                $query = $query->whereDoesntHave('truck');
            } elseif ($type == 2) {
                $query = $query->whereHas('truck');
            }
        }

        if ($status) {
            $packsCountQuery = '(SELECT COUNT(*) FROM packs WHERE orders.id = packs.order_id)';
            $deliveredPacksCountQuery = '(SELECT COUNT(*) FROM packs WHERE orders.id = packs.order_id AND packs.status = ' . Pack::DELIVERED . ')';

            if ($status == Pack::IN_PROGRESS) {
                $query = $query
                    ->whereDoesntHave('packs')
                    ->orWhereRaw("$packsCountQuery > $deliveredPacksCountQuery");
            } elseif ($status == Pack::DELIVERED) {
                $query = $query
                    ->whereHas('packs')
                    ->whereRaw("$packsCountQuery = $deliveredPacksCountQuery");
            }
        }

        if ($statusText) {
            $query = $query->whereRaw("$selectStatusText = '$statusText'");
        }

        $query = $query
            ->orderBy('id', 'DESC')
            ->orderBy('truck_id', 'ASC');

        $orders = $query->paginate($perPage);

        if (isSeller()) {
            $allOrders = Order::with(['customer'])
                ->orderBy('id', 'DESC')
                ->whereHas('customer', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->get();
        } else {
            $allOrders = Order::with(['customer'])
                ->orderBy('id', 'DESC')
                ->get();
        }

        if ($customerCode === Customer::NONAME_CODE) {
            $allOrders = Order::with(['customer'])
                ->orderBy('id', 'DESC')
                ->whereHas('customer', function ($query) {
                    $query->where('code', Customer::NONAME_CODE);
                })
                ->get();
        }

        $customers = Customer::orderBy('id', 'DESC')->get();
        $trucks = Truck::all();

        // $allOrders = [];
        $customers = [];
        // $trucks = [];

        return view('app.order.index', [
            'orders' => $orders,
            'allOrders' => $allOrders,
            'customers' => $customers,
            'trucks' => $trucks,
        ]);
    }

    public function getOrdersFromLocation(Location $location, Request $request)
    {
        $query = Order::with([
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
        ]);

        $name = $request->get('name');
        $id = $request->get('id');
        $bill = $request->get('bill');
        $customerCode = $request->get('customer_code');
        $productName = $request->get('product_name');
        $month = $request->get('month');
        $type = $request->get('type');
        $status = $request->get('status');
        $statusText = $request->get('status_text');
        $perPage = $request->get('per_page', 50);

        $selectIsCalculatedCost = "taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0";
        $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
        $selectFareByWeight = '( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) )';
        $selectFareByCubicMeters = '( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) )';
        $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
        $selectRevenue = "IFNULL($selectFare, 0) + IFNULL(taxes, 0) + IFNULL($selectOtherCosts, 0)";
        $selectPaid = 'IFNULL((SELECT SUM( IFNULL(payments.amount, 0) ) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL LIMIT 1), 0)';
        $selectDebt = "IFNULL($selectRevenue, 0) - IFNULL($selectPaid, 0)";

        $selectPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ";
        $selectDeliveredPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id AND packs.status = " . Pack::DELIVERED;

        $selectStatus = "
            IF( 
                ($selectPacksCount) = 0,

                " . Pack::IN_PROGRESS . ",

                IF(
                    ($selectPacksCount) = ($selectDeliveredPacksCount),

                    " . Pack::DELIVERED . ",

                    " . Pack::IN_PROGRESS . "
                )
            )
        ";

        $selectStatusText = "
            IF(
                truck_id IS NULL,

                '" . Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA . "',

                IF(
                    $selectStatus = " . PACK::DELIVERED . ",

                    IF(
                        $selectIsCalculatedCost = 1,

                        IF(
                            $selectDebt = 0,

                            '" . Order::STATUS_TEXT_COMPLETED . "',

                            '" . Order::STATUS_TEXT_WAIT_FOR_PAYING . "'
                        ),

                        '" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "'
                    ),

                    '" . Order::STATUS_TEXT_UNDELIVERED . "'
                )
            )
        ";

        $excludeIds = Customer::whereIn('code', [Customer::NONAME_CODE, Customer::EXPRESS_CODE])->get()->pluck('id')->join(',');

        $query = $query
            ->selectRaw('orders.*, trucks.deleted_at AS truck_deleted_at')
            ->leftJoin('trucks', 'orders.truck_id', 'trucks.id');

        if (
            !auth()->user()->is_admin
            && !auth()->user()->is_accountant
            && !auth()->user()->is_cn_inventory
        ) {
            $query = $query->whereHas('customer', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        if ($name) {
            $query = $query->where('name', 'LIKE', "%$name%");
        }

        if ($bill) {
            $query = $query->where('bill', 'LIKE', "%$bill%");
        }

        if ($productName) {
            $query = $query->where('product_name', 'LIKE', "%$productName%");
        }

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $query = $query
                    ->whereMonth('created_at', $date->format('m'))
                    ->whereYear('created_at', $date->format('Y'));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if ($type) {
            if ($type == 1) {
                $query = $query
                    ->where('location_id', $location->id)
                    ->whereDoesntHave('truck');
            } elseif ($type == 2) {
                $query = $query
                    ->where('location_id', $location->id)
                    ->whereHas('truck');
            }
        } else {
        }

        if ($status) {
            $packsCountQuery = '(SELECT COUNT(*) FROM packs WHERE orders.id = packs.order_id)';
            $deliveredPacksCountQuery = '(SELECT COUNT(*) FROM packs WHERE orders.id = packs.order_id AND packs.status = ' . Pack::DELIVERED . ')';

            if ($status == Pack::IN_PROGRESS) {
                $query = $query
                    ->whereDoesntHave('packs')
                    ->orWhereRaw("$packsCountQuery > $deliveredPacksCountQuery");
            } elseif ($status == Pack::DELIVERED) {
                $query = $query
                    ->whereHas('packs')
                    ->whereRaw("$packsCountQuery = $deliveredPacksCountQuery");
            }
        }

        if ($statusText) {
            $query = $query->whereRaw("$selectStatusText = '$statusText'");
        }

        $whereRaw = "( location_id = {$location->id} OR location_id IS NULL ) AND (customer_id NOT IN ($excludeIds))";
        $whereRaw = "( location_id = {$location->id} ) AND (customer_id NOT IN ($excludeIds))";

        if ($id) {
            $whereRaw = "( location_id = {$location->id} OR location_id IS NULL) AND orders.id = $id AND (customer_id NOT IN ($excludeIds))";
            $whereRaw = "( location_id = {$location->id} ) AND orders.id = $id AND (customer_id NOT IN ($excludeIds))";
        }

        $query =
            $query
            ->whereRaw($whereRaw);

        $query = $query
            ->orderBy('location_id', 'DESC')
            ->orderBy('id', 'DESC')
            ->orderBy('truck_id', 'ASC');

        $orders =
            $query->paginate($perPage);

        if (isSeller()) {
            $allOrders = Order::with(['customer'])
                ->orderBy('id', 'DESC')
                ->whereHas('customer', function ($query) {
                    $query->where('user_id', auth()->id());
                })
                ->whereHas('customer', function ($q) {
                    $q->whereNotIn('code', [Customer::NONAME_CODE, Customer::EXPRESS_CODE]);
                })
                ->get();
        } else {
            $allOrders = Order::with(['customer'])
                ->whereHas('customer', function ($q) {
                    $q->whereNotIn('code', [Customer::NONAME_CODE, Customer::EXPRESS_CODE]);
                })
                ->orderBy('id', 'DESC')
                ->get();
        }

        if ($customerCode === Customer::NONAME_CODE) {
            $allOrders = Order::with(['customer'])
                ->orderBy('id', 'DESC')
                ->whereHas('customer', function ($query) {
                    $query->where('code', Customer::NONAME_CODE);
                })
                ->get();
        }

        $customers = Customer::orderBy('id', 'DESC')->get();
        $trucks = Truck::all();

        return view('app.order.location.index', [
            'orders' => $orders,
            'allOrders' => $allOrders,
            'customers' => $customers,
            'trucks' => $trucks,
            'location' => $location,
        ]);
    }

    public function getOrdersOfNoName(Request $request)
    {
        $query = Order::query();

        $name = $request->get('name');
        $id = $request->get('id');
        $bill = $request->get('bill');
        $productName = $request->get('product_name');
        $month = $request->get('month');
        $type = $request->get('type');
        $status = $request->get('status');
        $statusText = $request->get('status_text');
        $perPage = $request->get('per_page', 50);

        $selectIsCalculatedCost = "taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0";
        $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
        $selectFareByWeight = '( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) )';
        $selectFareByCubicMeters = '( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) )';
        $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
        $selectRevenue = "IFNULL($selectFare, 0) + IFNULL(taxes, 0) + IFNULL($selectOtherCosts, 0)";
        $selectPaid = 'IFNULL((SELECT SUM( IFNULL(payments.amount, 0) ) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL LIMIT 1), 0)';
        $selectDebt = "IFNULL($selectRevenue, 0) - IFNULL($selectPaid, 0)";


        $selectPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ";
        $selectDeliveredPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id AND packs.status = " . Pack::DELIVERED;

        $selectStatus = "
            IF( 
                ($selectPacksCount) = 0,

                " . Pack::IN_PROGRESS . ",

                IF(
                    ($selectPacksCount) = ($selectDeliveredPacksCount),

                    " . Pack::DELIVERED . ",

                    " . Pack::IN_PROGRESS . "
                )
            )
        ";

        $selectStatusText = "
            IF(
                truck_id IS NULL,

                '" . Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA . "',

                IF(
                    $selectStatus = " . PACK::DELIVERED . ",

                    IF(
                        $selectIsCalculatedCost = 1,

                        IF(
                            $selectDebt = 0,

                            '" . Order::STATUS_TEXT_COMPLETED . "',

                            '" . Order::STATUS_TEXT_WAIT_FOR_PAYING . "'
                        ),

                        '" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "'
                    ),

                    '" . Order::STATUS_TEXT_UNDELIVERED . "'
                )
            )
        ";

        $query = $query
            ->select()
            ->addSelect(DB::raw("($selectIsCalculatedCost) AS is_calculated_cost2"))
            ->addSelect(DB::raw("($selectStatusText) AS status_text2"))
            ->addSelect(DB::raw("($selectDebt) AS debt2"));

        $query = $query->whereHas(
            'customer',
            function ($q) {
                $q->where('code', Customer::NONAME_CODE);
            }
        );

        if ($name) {
            $query = $query->where('name', 'LIKE', "%$name%");
        }

        if ($id) {
            $query = $query->where('id', $id);
        }

        if ($bill) {
            $query = $query->where('bill', 'LIKE', "%$bill%");
        }

        if ($productName) {
            $query = $query->where('product_name', 'LIKE', "%$productName%");
        }

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $query = $query
                    ->whereMonth('created_at', $date->format('m'))
                    ->whereYear('created_at', $date->format('Y'));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if ($type) {
            if ($type == 1) {
                $query = $query->whereDoesntHave('truck');
            } elseif ($type == 2) {
                $query = $query->whereHas('truck');
            }
        }

        if ($status) {
            $packsCountQuery = '(SELECT COUNT(*) FROM packs WHERE orders.id = packs.order_id)';
            $deliveredPacksCountQuery = '(SELECT COUNT(*) FROM packs WHERE orders.id = packs.order_id AND packs.status = ' . Pack::DELIVERED . ')';

            if ($status == Pack::IN_PROGRESS) {
                $query = $query
                    ->whereDoesntHave('packs')
                    ->orWhereRaw("$packsCountQuery > $deliveredPacksCountQuery");
            } elseif ($status == Pack::DELIVERED) {
                $query = $query
                    ->whereHas('packs')
                    ->whereRaw("$packsCountQuery = $deliveredPacksCountQuery");
            }
        }

        if ($statusText) {
            $query = $query->whereRaw("$selectStatusText = '$statusText'");
        }

        $query = $query->with([
            'truck',
            'customer',
        ])
            ->orderBy('id', 'DESC')
            ->orderBy('truck_id', 'ASC');

        $orders = $query->paginate($perPage);

        $allOrders = Order::orderBy('id', 'DESC')
            ->whereHas('customer', function ($query) {
                $query->where('code', Customer::NONAME_CODE);
            })
            ->get();
        $customers = Customer::orderBy('id', 'DESC')->get();
        $trucks = Truck::all();

        return view('app.order.noname', [
            'orders' => $orders,
            'allOrders' => $allOrders,
            'customers' => $customers,
            'trucks' => $trucks,
        ]);
    }

    public function getOrdersOfExpress(Request $request)
    {
        $query = Order::with([
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
        ]);

        $name = $request->get('name');
        $id = $request->get('id');
        $bill = $request->get('bill');
        $productName = $request->get('product_name');
        $month = $request->get('month');
        $type = $request->get('type');
        $status = $request->get('status');
        $statusText = $request->get('status_text');
        $perPage = $request->get('per_page', 50);

        $selectIsCalculatedCost = "taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0";
        $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
        $selectFareByWeight = '( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) )';
        $selectFareByCubicMeters = '( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) )';
        $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
        $selectRevenue = "IFNULL($selectFare, 0) + IFNULL(taxes, 0) + IFNULL($selectOtherCosts, 0)";
        $selectPaid = 'IFNULL((SELECT SUM( IFNULL(payments.amount, 0) ) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL LIMIT 1), 0)';
        $selectDebt = "IFNULL($selectRevenue, 0) - IFNULL($selectPaid, 0)";


        $selectPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ";
        $selectDeliveredPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id AND packs.status = " . Pack::DELIVERED;

        $selectStatus = "
            IF( 
                ($selectPacksCount) = 0,

                " . Pack::IN_PROGRESS . ",

                IF(
                    ($selectPacksCount) = ($selectDeliveredPacksCount),

                    " . Pack::DELIVERED . ",

                    " . Pack::IN_PROGRESS . "
                )
            )
        ";

        $selectStatusText = "
            IF(
                truck_id IS NULL,

                '" . Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA . "',

                IF(
                    $selectStatus = " . PACK::DELIVERED . ",

                    IF(
                        $selectIsCalculatedCost = 1,

                        IF(
                            $selectDebt = 0,

                            '" . Order::STATUS_TEXT_COMPLETED . "',

                            '" . Order::STATUS_TEXT_WAIT_FOR_PAYING . "'
                        ),

                        '" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "'
                    ),

                    '" . Order::STATUS_TEXT_UNDELIVERED . "'
                )
            )
        ";

        $query = $query
            ->select()
            ->addSelect(DB::raw("($selectIsCalculatedCost) AS is_calculated_cost2"))
            ->addSelect(DB::raw("($selectStatusText) AS status_text2"))
            ->addSelect(DB::raw("($selectDebt) AS debt2"));

        $query = $query->whereHas(
            'customer',
            function ($q) {
                $q->where('code', Customer::EXPRESS_CODE);
            }
        );

        if ($name) {
            $query = $query->where('name', 'LIKE', "%$name%");
        }

        if ($id) {
            $query = $query->where('id', $id);
        }

        if ($bill) {
            $query = $query->where('bill', 'LIKE', "%$bill%");
        }

        if ($productName) {
            $query = $query->where('product_name', 'LIKE', "%$productName%");
        }

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $query = $query
                    ->whereMonth('created_at', $date->format('m'))
                    ->whereYear('created_at', $date->format('Y'));
            } catch (\Throwable $th) {
                //throw $th;
            }
        }

        if ($type) {
            if ($type == 1) {
                $query = $query->whereDoesntHave('truck');
            } elseif ($type == 2) {
                $query = $query->whereHas('truck');
            }
        }

        if ($status) {
            $packsCountQuery = '(SELECT COUNT(*) FROM packs WHERE orders.id = packs.order_id)';
            $deliveredPacksCountQuery = '(SELECT COUNT(*) FROM packs WHERE orders.id = packs.order_id AND packs.status = ' . Pack::DELIVERED . ')';

            if ($status == Pack::IN_PROGRESS) {
                $query = $query
                    ->whereDoesntHave('packs')
                    ->orWhereRaw("$packsCountQuery > $deliveredPacksCountQuery");
            } elseif ($status == Pack::DELIVERED) {
                $query = $query
                    ->whereHas('packs')
                    ->whereRaw("$packsCountQuery = $deliveredPacksCountQuery");
            }
        }

        if ($statusText) {
            $query = $query->whereRaw("$selectStatusText = '$statusText'");
        }

        $query = $query
            ->orderBy('id', 'DESC')
            ->orderBy('truck_id', 'ASC');

        $orders = $query->paginate($perPage);

        $allOrders = Order::with(['customer'])
            ->orderBy('id', 'DESC')
            ->whereHas('customer', function ($query) {
                $query->where('code', Customer::EXPRESS_CODE);
            })
            ->get();
        $customers = Customer::orderBy('id', 'DESC')->get();
        $trucks = Truck::all();

        return view('app.order.express', [
            'orders' => $orders,
            'allOrders' => $allOrders,
            'customers' => $customers,
            'trucks' => $trucks,
        ]);
    }

    public function vietnameseInventory(Request $request)
    {
        $query = Order::with([
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
        ]);

        $name = $request->get('name');
        $id = $request->get('id');
        $customerId = $request->get('customer_id');
        $bill = $request->get('bill');
        $customerCode = $request->get('customer_code');
        $productName = $request->get('product_name');
        $month = $request->get('month');
        $type = $request->get('type');
        $statusText = $request->get('status_text', Order::STATUS_TEXT_UNDELIVERED);
        $perPage = $request->get('per_page', 50);

        $selectIsCalculatedCost = "taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0";
        $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
        $selectFareByWeight = '( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) )';
        $selectFareByCubicMeters = '( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) )';
        $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
        $selectRevenue = "IFNULL($selectFare, 0) + IFNULL(taxes, 0) + IFNULL($selectOtherCosts, 0)";
        $selectPaid = 'IFNULL((SELECT SUM( IFNULL(payments.amount, 0) ) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL LIMIT 1), 0)';
        $selectDebt = "IFNULL($selectRevenue, 0) - IFNULL($selectPaid, 0)";

        $selectPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ";
        $selectDeliveredPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id AND packs.status = " . Pack::DELIVERED;

        $selectStatus = "
            IF( 
                ($selectPacksCount) = 0,

                status,

                IF(
                    ($selectPacksCount) = ($selectDeliveredPacksCount),

                    " . Pack::DELIVERED . ",

                    " . Pack::IN_PROGRESS . "
                )
            )
        ";

        $selectStatusText = "
            IF(
                truck_id IS NULL,

                '" . Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA . "',

                IF(
                    $selectStatus = " . PACK::DELIVERED . ",

                    IF(
                        $selectIsCalculatedCost = 1,

                        IF(
                            $selectDebt = 0,

                            '" . Order::STATUS_TEXT_COMPLETED . "',

                            '" . Order::STATUS_TEXT_WAIT_FOR_PAYING . "'
                        ),

                        '" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "'
                    ),

                    '" . Order::STATUS_TEXT_UNDELIVERED . "'
                )
            )
        ";

        $query = $query
            ->select();

        if (!auth()->user()->is_admin && !auth()->user()->is_accountant) {
            $query = $query->whereHas('customer', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        if ($name) {
            $query = $query->where('name', 'LIKE', "%$name%");
        }

        if ($id) {
            $query = $query->where('id', $id);
        }

        if ($customerId) {
            $query = $query->where('customer_id', $customerId);
        }

        if ($bill) {
            $query = $query->where('bill', 'LIKE', "%$bill%");
        }

        if ($customerCode) {
            $query = $query->whereHas('customer', function ($q) use ($customerCode) {
                $q->where('code', 'LIKE', "%$customerCode%");
            });
        }

        if ($productName) {
            $query = $query->where('product_name', 'LIKE', "%$productName%");
        }

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $query = $query
                    ->whereMonth('created_at', $date->format('m'))
                    ->whereYear('created_at', $date->format('Y'));
            } catch (Exception $e) {
            }
        }

        if ($type) {
            if ($type == 1) {
                $query = $query->whereDoesntHave('truck');
            } elseif ($type == 2) {
                $query = $query->whereHas('truck');
            }
        }

        if ($statusText) {
            $query = $query->whereRaw("$selectStatusText = '$statusText'");
        }

        $query = $query
            ->whereRaw(" ( $selectStatusText ) IN ('" . Order::STATUS_TEXT_UNDELIVERED . "','" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "') ")
            ->orderBy('truck_id', 'ASC');

        $orders = $query->paginate($perPage);
        $total = $orders->total();

        $allOrders = Order::with(['customer'])
            ->orderBy('id', 'DESC')->get();
        $customers = Customer::orderBy('id', 'DESC')->get();

        return view('app.order.vietnamese_inventory', [
            'total' => $total,
            'orders' => $orders,
            'allOrders' => $allOrders,
            'customers' => $customers,
        ]);
    }

    public function vietnameseInventoryFromLocation(Location $location, Request $request)
    {
        $query = Order::with([
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
        ]);

        $name = $request->get('name');
        $id = $request->get('id');
        $customerId = $request->get('customer_id');
        $bill = $request->get('bill');
        $customerCode = $request->get('customer_code');
        $productName = $request->get('product_name');
        $month = $request->get('month');
        $type = $request->get('type');
        $statusText = $request->get('status_text', Order::STATUS_TEXT_UNDELIVERED);
        $perPage = $request->get('per_page', 50);

        $selectIsCalculatedCost = "taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0";
        $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
        $selectFareByWeight = '( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) )';
        $selectFareByCubicMeters = '( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) )';
        $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
        $selectRevenue = "IFNULL($selectFare, 0) + IFNULL(taxes, 0) + IFNULL($selectOtherCosts, 0)";
        $selectPaid = 'IFNULL((SELECT SUM( IFNULL(payments.amount, 0) ) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL LIMIT 1), 0)';
        $selectDebt = "IFNULL($selectRevenue, 0) - IFNULL($selectPaid, 0)";

        $selectPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ";
        $selectDeliveredPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id AND packs.status = " . Pack::DELIVERED;

        $selectStatus = "
            IF( 
                ($selectPacksCount) = 0,

                status,

                IF(
                    ($selectPacksCount) = ($selectDeliveredPacksCount),

                    " . Pack::DELIVERED . ",

                    " . Pack::IN_PROGRESS . "
                )
            )
        ";

        $selectStatusText = "
            IF(
                truck_id IS NULL,

                '" . Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA . "',

                IF(
                    $selectStatus = " . PACK::DELIVERED . ",

                    IF(
                        $selectIsCalculatedCost = 1,

                        IF(
                            $selectDebt = 0,

                            '" . Order::STATUS_TEXT_COMPLETED . "',

                            '" . Order::STATUS_TEXT_WAIT_FOR_PAYING . "'
                        ),

                        '" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "'
                    ),

                    '" . Order::STATUS_TEXT_UNDELIVERED . "'
                )
            )
        ";

        $query = $query
            ->select();

        if (
            !auth()->user()->is_admin
            && !auth()->user()->is_accountant
            && !auth()->user()->is_vn_inventory
        ) {
            $query = $query->whereHas('customer', function ($q) {
                $q->where('user_id', auth()->id());
            });
        }

        if ($name) {
            $query = $query->where('name', 'LIKE', "%$name%");
        }

        if ($id) {
            $query = $query->where('id', $id);
        }

        if ($customerId) {
            $query = $query->where('customer_id', $customerId);
        }

        if ($bill) {
            $query = $query->where('bill', 'LIKE', "%$bill%");
        }

        if ($customerCode) {
            $query = $query->whereHas('customer', function ($q) use ($customerCode) {
                $q->where('code', 'LIKE', "%$customerCode%");
            });
        }

        if ($productName) {
            $query = $query->where('product_name', 'LIKE', "%$productName%");
        }

        if ($month) {
            try {
                $date = Carbon::createFromFormat('Y-m', $month);
                $query = $query
                    ->whereMonth('created_at', $date->format('m'))
                    ->whereYear('created_at', $date->format('Y'));
            } catch (Exception $e) {
            }
        }

        if ($type) {
            if ($type == 1) {
                $query = $query->whereDoesntHave('truck');
            } elseif ($type == 2) {
                $query = $query->whereHas('truck');
            }
        }

        if ($statusText) {
            $query = $query->whereRaw("$selectStatusText = '$statusText'");
        }

        $query = $query->whereHas('truck.currentLocation', function ($q) use ($location) {
            $q->where('id', $location->id);
        });

        $query = $query
            ->whereRaw(" ( $selectStatusText ) IN ('" . Order::STATUS_TEXT_UNDELIVERED . "','" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "') ")
            ->orderBy('truck_id', 'ASC');

        $orders = $query->paginate($perPage);
        $total = $orders->total();

        $allOrders = Order::with(['customer'])
            ->orderBy('id', 'DESC')->get();
        $customers = Customer::orderBy('id', 'DESC')->get();

        return view('app.order.vietnamese_inventory', [
            'total' => $total,
            'orders' => $orders,
            'allOrders' => $allOrders,
            'customers' => $customers,
            'location' => $location
        ]);
    }

    public function getUnpaidOrders(Request $request)
    {
        $perPage = $request->get('per_page', 50);
        $locationId = $request->get('location_id');

        $query = Order::query();

        $selectIsCalculatedCost = "taxes1 > 0 OR taxes2 > 0 OR cost_vietnam > 0 OR fare_unit_by_weight > 0 OR fare_unit_by_cubic_meters > 0";
        $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
        $selectFareByWeight = '( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) )';
        $selectFareByCubicMeters = '( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) )';
        $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
        $selectRevenue = "IFNULL($selectFare, 0) + IFNULL(taxes, 0) + IFNULL($selectOtherCosts, 0)";
        $selectPaid = 'IFNULL((SELECT SUM( IFNULL(payments.amount, 0) ) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL LIMIT 1), 0)';
        $selectDebt = "IFNULL($selectRevenue, 0) - IFNULL($selectPaid, 0)";


        $selectPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ";
        $selectDeliveredPacksCount = " SELECT COUNT(*) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id AND packs.status = " . Pack::DELIVERED;

        $selectStatus = "
            IF( 
                ($selectPacksCount) = 0,

                " . Pack::IN_PROGRESS . ",

                IF(
                    ($selectPacksCount) = ($selectDeliveredPacksCount),

                    " . Pack::DELIVERED . ",

                    " . Pack::IN_PROGRESS . "
                )
            )
        ";

        $selectStatusText = "
            IF(
                truck_id IS NULL,

                '" . Order::STATUS_TEXT_IS_RECEIVED_IN_CHINA . "',

                IF(
                    $selectStatus = " . PACK::DELIVERED . ",

                    IF(
                        $selectIsCalculatedCost = 1,

                        IF(
                            $selectDebt = 0,

                            '" . Order::STATUS_TEXT_COMPLETED . "',

                            '" . Order::STATUS_TEXT_WAIT_FOR_PAYING . "'
                        ),

                        '" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "'
                    ),

                    '" . Order::STATUS_TEXT_UNDELIVERED . "'
                )
            )
        ";

        $query = $query->with([
            'truck',
            'customer',
        ])
            ->whereHas('truck.currentLocation', function ($q) use ($locationId) {
                $q->where('id', $locationId);
            })
            ->whereRaw("$selectStatusText = '" . Order::STATUS_TEXT_IS_NOT_CALCULATED_COST . "'")
            ->orderBy('truck_id', 'ASC');

        $orders = $query->paginate($perPage);

        return view('app.order.unpaid', [
            'orders' => $orders,
        ]);
    }

    public function create(Request $request, Uploader $uploader)
    {
        Gate::authorize('has-permissions', 'create.order');

        $data = $request->all();

        $validator = Validator::make($data, [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('orders')->whereNull('deleted_at')
            ],
            'bill' => [
                'required',
                'string',
                'max:255',
                Rule::unique('orders')->whereNull('deleted_at')
            ],
            'product_name' => [
                'nullable',
                'string',
            ],
            'images' => [
                'nullable',
                'max:4'
            ],
            'images.*' => [
                'image',
                'max:2048',
            ],
            'weight' => [
                'nullable',
                'numeric',
                'gt:0',
            ],
            'cost_china1' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'cost_china2' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'note' => [
                'nullable',
                'string',
            ],
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')
            ],
        ]);

        $redirect = $data['redirect'];

        if ($validator->fails()) {
            return redirect()
                ->to("$redirect#create-order" ?? 'customers#create-order')
                ->withErrors($validator)
                ->withInput();
        }

        $rmbToVND = Option::where('name', 'rmb_to_vnd')->first()->value;

        $data = [
            'location_id' => Arr::get($data, 'location_id'),
            'customer_id' => $data['customer_id'],
            'code' => $data['code'],
            'bill' => $data['bill'],

            'product_name' => $data['product_name'],
            'weight' => $data['weight'],
            'cost_china' => 0,
            'cost_china1' => $data['cost_china1'],
            'cost_china2' => $data['cost_china2'],

            'fare_unit_by_weight' => 0,
            'fare_unit_by_cubic_meters' => 0,

            'rmb_to_vnd' => $rmbToVND,

            'note' => $data['note'],
        ];

        $images = $request->file('images', []);
        $data['images'] = $uploader->upload($images);

        Order::create($data);

        return $redirect ? redirect()->to($redirect) : redirect()->route('order.index');
    }

    public function show(Order $order)
    {
        $customers = Customer::orderBy('id', 'DESC')->get();
        $order->loadCount(['packs']);

        return view('app.order.show', [
            'order' => $order,
            'customers' => $customers,
        ]);
    }

    public function update(Request $request, Order $order, Uploader $uploader)
    {
        Gate::authorize('update-order-noname', $order);

        // DB::beginTransaction();

        $data = $request->all();

        $validator = Validator::make($data, [
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('orders')->ignore($order->id, 'id')->whereNull('deleted_at')
            ],
            'bill' => [
                'required',
                'string',
                'max:255',
                Rule::unique('orders')->ignore($order->id, 'id')->whereNull('deleted_at')
            ],
            'product_name' => [
                'nullable',
                'string',
            ],
            'image' => [
                'nullable',
                'sometimes',
                'image',
            ],
            'images' => [
                'nullable',
                'max:4'
            ],
            'images.*' => [
                'image',
                'max:2048',
            ],
            'weight' => [
                'nullable',
                'numeric',
                'gt:0',
            ],
            'cost_china1' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'cost_china2' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'note' => [
                'nullable',
                'string',
            ],
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')
            ],
            'rmb_to_vnd' => [
                'required',
                'numeric',
                'gte:0',
            ],
        ]);

        $redirect = $data['redirect'];

        if ($validator->fails()) {
            return redirect()
                ->to("$redirect#update-order-{$order->id}")
                ->withErrors($validator)
                ->withInput();
        }

        $data['images'] = $order->images;
        $images = $request->file('images', []);
        if (count($images) > 0) {
            $data['images'] = $uploader->upload($images);
        }

        $order->fill($data);
        $order->save();

        // foreach ($order->getChanges() as $attribute => $value) {
        //     if ($attribute !== 'updated_at') {
        //         Gate::authorize('has-permissions', 'update.order.' . $attribute);
        //     }
        // }

        // DB::commit();

        return redirect()->back();
    }

    public function calculateCost(Request $request, Order $order)
    {
        if (auth()->user()->role !== User::ROLE_ACCOUNTANT && auth()->user()->role !== User::ROLE_VN_INVENTORY) {
            Gate::authorize('has-permissions', 'update.order.calculate_cost');
        }

        DB::beginTransaction();

        $data = $request->all();

        $validator = Validator::make($data, [
            'taxes1' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'taxes2' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'cost_vietnam' => [
                'required',
                'numeric',
                // 'gte:0',
            ],
            'fare_unit_by_weight' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'fare_unit_by_cubic_meters' => [
                'required',
                'numeric',
                'gte:0',
            ],
        ]);

        $redirect = $data['redirect'];

        if ($validator->fails()) {
            return redirect()
                ->to("$redirect#update-order-{$order->id}")
                ->withErrors($validator)
                ->withInput();
        }

        $data['taxes'] = $data['taxes1'] + $data['taxes2'];
        $order->fill($data);
        $order->save();

        foreach ($order->getChanges() as $attribute => $value) {
            if ($attribute !== 'updated_at') {
                if (auth()->user()->role !== User::ROLE_ACCOUNTANT && auth()->user()->role !== User::ROLE_VN_INVENTORY) {
                    Gate::authorize('has-permissions', 'update.order.' . $attribute);
                }
            }
        }

        DB::commit();

        return redirect()->back();
    }

    public function calculateCosts(Request $request)
    {
        Gate::authorize('has-permissions', 'update.order.calculate_cost');

        DB::beginTransaction();

        $data = $request->all();

        $validator = Validator::make($data, [
            'order_ids' => [
                'required',
                'array',
            ],
            'order_ids.*' => [
                'required',
                'integer',
            ],
            'taxes1' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'taxes2' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'cost_vietnam' => [
                'required',
                'numeric',
            ],
            'fare_unit_by_weight' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'fare_unit_by_cubic_meters' => [
                'required',
                'numeric',
                'gte:0',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data['taxes'] = $data['taxes1'] + $data['taxes2'];
        $orderIds = $data['order_ids'];

        unset($data["_token"]);
        unset($data["order_ids"]);

        Order::whereIn('id', $orderIds)->update($data);

        DB::commit();

        return redirect()->back();
    }

    public function destroy(Order $order)
    {
        Gate::authorize('can-delete-order');

        $order->delete();

        return redirect()->back();
    }

    public function updateStatus(Order $order, Request $request, Webhook $webhook)
    {
        $data = $request->all();

        if ($order->can_delivery && auth()->check()) {
            $order->update([
                'status' => Pack::DELIVERED,
                'delivery_date' => Arr::get(
                    $data,
                    'delivery_date',
                    Carbon::now()
                ),
                'driver_phone' => $data['driver_phone'],
                'license_plate_number' => $data['license_plate_number']
            ]);

            // $order->status = Pack::DELIVERED;
            // $order->delivery_date = Arr::get(
            //     $data,
            //     'delivery_date',
            //     Carbon::now()
            // );
            // $order->driver_phone = $data['driver_phone'];
            // $order->license_plate_number = $data['license_plate_number'];
            // $order->save();

            $order->packs()->update([
                'status' => $data['status']
            ]);

            Log::info("#{$order->id} status_text: {$order->status_text} ");
            Log::info("#{$order->id} status: {$order->status} ");
            Log::info("#{$order->id} delivered");

            $resp = $webhook->postToWebhookDotSite([
                'delivery_date' => $order->delivery_date,
                'code' => $order->code,
                'customer_code' => optional($order->customer)->code,
                'driver_phone' => $order->driver_phone,
                'license_plate_number' => $order->license_plate_number
            ]);

            $body = $resp->getBody();

            Log::info("Post webhook body: {$body}");

            $webhook->test([
                'delivery_date' => $order->delivery_date,
                'code' => $order->code,
                'customer_code' => optional($order->customer)->code,
                'driver_phone' => $order->driver_phone,
                'license_plate_number' => $order->license_plate_number
            ]);
        }

        return response()
            ->json(['id' => $order->id]);
    }

    public function updateNoteInList(Order $order, Request $request)
    {
        $data = $request->all();

        $order->fill([
            'note_in_list' => $data['note_in_list']
        ]);
        $order->save();

        return response()
            ->json($order);
    }

    public function updateNoteInTruck(Order $order, Request $request)
    {
        $data = $request->all();

        $order->fill([
            'note_in_truck' => $data['note_in_truck']
        ]);
        $order->save();

        return response()
            ->json($order);
    }

    public function updateNoteInVnInventory(Order $order, Request $request)
    {
        $data = $request->all();

        $order->fill([
            'note_in_vn_inventory' => $data['note_in_vn_inventory']
        ]);
        $order->save();

        return response()
            ->json($order);
    }

    public function processExpressOrders(Request $request)
    {
        $rawOrders = [];

        try {
            $file = $request->file('file');

            $newFilename = Str::random() . '.' . $file->getClientOriginalExtension();

            $_file = $file->move(public_path('files'), $newFilename);

            $reader = new Xlsx();

            $reader->setReadDataOnly(true);

            // $spreadsheet = $reader->load(public_path('files/NhapHangT11.xlsx'));
            $spreadsheet = $reader->load(public_path('files/' . $_file->getFilename()));

            $sheet = $spreadsheet->getSheet($spreadsheet->getFirstSheetIndex());

            $data = $sheet->toArray();
            unset($data[0]);

            foreach ($data as $item) {
                $createdAt =  Carbon::now();

                try {
                    if (Date::excelToTimestamp($item[0]) != 0) {
                        $createdAt = Carbon::createFromTimestamp(Date::excelToTimestamp($item[0]))->toDateTimeString();
                    }

                    Log::info("excelToTimestamp : " . Date::excelToTimestamp($item[0]));
                    Log::info("createdAt : {$createdAt}");
                } catch (\Throwable $th) {
                    //throw $th;
                }

                try {
                    $code = trim($item[4]);
                    $note = $item[2];
                    $costChina1 = floatval($item[5]);
                    $costChina2 = floatval($item[6]);

                    if ($code) {
                        $rawOrders[] = [
                            'created_at' => $createdAt,
                            'code' => $code,
                            'cost_china1' => $costChina1,
                            'cost_china2' => $costChina2,
                            'note' => $note,
                        ];
                    }
                } catch (Exception $e) {
                }
            }
        } catch (\Throwable $th) {
        }

        $customer = Customer::where('code', Customer::EXPRESS_CODE)->first();
        $insertData = [];

        foreach ($rawOrders as $rawOrder) {
            $check = Order::where('code', $rawOrder['code'])->first();

            if (!$check) {
                $rawOrder['customer_id'] = $customer->id;
                $rawOrder['bill'] = $rawOrder['code'];
                $rawOrder['fare_unit_by_weight'] = 0;
                $rawOrder['fare_unit_by_cubic_meters'] = 0;

                $insertData[] = $rawOrder;
            }
        }

        Order::insert($insertData);

        return redirect()->back();
    }

    public function merge(Request $request, Order $order)
    {
        DB::beginTransaction();

        $data = $request->all();

        $validator = Validator::make($data, [
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to("/orders/vietnamese_inventory#merge-order-{$order->id}")
                ->withErrors($validator)
                ->withInput();
        }

        $order->customer_id = $data['customer_id'];
        $order->save();

        foreach ($order->getChanges() as $attribute => $value) {
            if ($attribute !== 'updated_at') {
                Gate::authorize('has-permissions', 'update.order.' . $attribute);
            }
        }

        DB::commit();

        return redirect()->route('order.show', ['order' => $order]);
    }

    public function bulkMerge(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'codes' => [
                'array',
            ],
            'codes.*' => [
                'string',
                Rule::exists('orders', 'code')
            ],
            'truck_id' => [
                'integer',
                'required',
                Rule::exists('trucks', 'id')
            ]
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->all()
            ]);
        }

        Order::whereIn('code', $data['codes'])->update([
            'truck_id' => $data['truck_id']
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    /**
     * Check if order ids are express
     */
    public function checkOrderCodes(Request $request)
    {
        $data = $request->all();

        $codes = Arr::get($data, 'order_codes', []);

        $expressCodes = Order::whereIn('code', $codes)
            ->whereHas('customer', function ($q) {
                $q->whereIn('code', [Customer::EXPRESS_CODE]);
            })->pluck('code');

        $otherCodes = collect($codes)->diff($expressCodes)->values()->all();

        return response()->json([
            'codes' => $expressCodes,
            'other_codes' => $otherCodes
        ]);
    }

    public function address(Request $request)
    {
        // $addresses = NotifyAddress::query()
        //     ->with([
        //         'order'
        //     ])
        //     ->orderBy('id', 'DESC')
        //     ->paginate();

        $addresses = NotifyAddress::query()
            ->with([
                'order.customer'
            ])
            ->whereHas('order', function ($query) {
                $query
                    ->whereNotNull('delivery_date')
                    ->whereDate('delivery_date', '>=', Carbon::now()->subDays(2));
            })
            ->orWhereHas('order', function ($query) {
                $query->whereNull('delivery_date');
            })
            ->orderBy('id', 'DESC')
            ->get();

        return view('app.order.address', [
            'addresses' => $addresses
        ]);
    }
}
