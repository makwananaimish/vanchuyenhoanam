<?php

use App\Customer;
use App\Location;
use App\Transaction;
use App\Truck;
use App\User;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

function getReportRevenue($orders = [])
{
    return collect($orders)->sum('revenue');
}

function getReportNetIncome($orders = [])
{
    $cubicMeters = collect($orders)->sum('cubic_meters');

    if (count($orders) === 0) {
        $costPerCubicMeters = 0;
    } else {
        $costPerCubicMeters = $cubicMeters == 0 ? 0 : $orders[0]->truck_total_cost / $cubicMeters;
    }

    return collect($orders)->sum(function ($order) use ($costPerCubicMeters) {
        // $tax = $order->taxes;
        // $otherCost = $order->other_costs;
        $tax = $order->tax;
        $otherCost = $order->other_cost;

        $cost = $order->cubic_meters * $costPerCubicMeters + $tax + $order->cost_china_vnd + $otherCost;
        $saleRevenue = 0.04 * $order->revenue - $otherCost - $order->cubic_meters * $costPerCubicMeters;
        $netIncome = $order->revenue - $saleRevenue - $cost;

        return $netIncome;
    });
}

function getCustomerRevenue($customerId)
{
    return  DB::table('orders')
        ->select(
            'id',
            'fare_unit_by_weight',
            'fare_unit_by_cubic_meters',
            'weight',
            DB::raw(' (SELECT SUM(packs.weight * packs.quantity) FROM packs WHERE orders.id = packs.order_id) AS weight_by_packs '),
            DB::raw(' (SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id) AS cubic_meters '),
            'cost_china',
            'cost_china1',
            'cost_china2',
            'cost_vietnam',
            'rmb_to_vnd',
            'taxes',
            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd ) AS cost_china_vnd'),
            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) ) AS other_costs'),
            DB::raw(' ( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) ) AS fare_by_weight'),
            DB::raw(' ( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) ) AS fare_by_cubic_meters')
        )
        ->where('customer_id', $customerId)
        ->get()
        ->map(function ($order) {
            $fare = 0;
            $fareByWeight = $order->fare_by_weight;
            $fareByCubicMeters = $order->fare_by_cubic_meters;
            if ($fareByWeight > $fareByCubicMeters) {
                $fare = $fareByWeight;
            } else {
                $fare = $fareByCubicMeters;
            }

            $order->fare = $fare;
            $order->revenue = $order->fare + $order->taxes + $order->other_costs;

            return $order;
        })->sum('revenue');
}

function getOrders($truckId = null, $departureMonth = null, $departureYear = null)
{
    $query = DB::table('orders');

    if ($truckId) {
        if (is_array($truckId)) {
            $query = $query
                ->whereIn('truck_id', $truckId);
        } else {
            $query = $query
                ->where('truck_id', $truckId);
        }
    }

    if (!is_null($departureMonth)) {
        $query = $query
            ->whereMonth('trucks.departure_date', $departureMonth);
    }

    if (!is_null($departureYear)) {
        $query = $query
            ->whereYear('trucks.departure_date', $departureYear);
    }

    $selectWeightFromPacks = 'COALESCE((SELECT SUM(weight * quantity) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL), 0)';
    $selectWeightFromColumn = 'COALESCE(weight, 0)';
    $selectWeight = "IF($selectWeightFromPacks > $selectWeightFromColumn, $selectWeightFromPacks, $selectWeightFromColumn)";

    $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
    $_selectOtherCosts = " IF( other_costs IS NULL, $selectOtherCosts, other_costs) ";
    $__selectOtherCosts = " IF( other_costs IS NULL, 0, other_costs) ";

    return $query
        ->select(
            'orders.id',
            'orders.code',
            'customer_id',
            'truck_id',

            'fare_unit_by_weight',
            'fare_unit_by_cubic_meters',

            'weight',
            DB::raw(' (SELECT SUM(packs.weight * packs.quantity) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL) AS weight_by_packs '),
            DB::raw(' (SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL) AS cubic_meters '),

            'cost_china',
            'cost_china1',
            'cost_china2',
            'cost_vietnam',

            'cost_per_weight',
            'cost_per_cubic_meters',

            'rmb_to_vnd',

            DB::raw('COALESCE(taxes1, 0) + COALESCE(taxes2, 0) AS taxes'),
            'taxes1',
            'taxes2',

            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd ) AS cost_china_vnd'),
            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd  ) AS cost_china1_vnd'),
            DB::raw(' ( COALESCE(cost_china2, 0) * rmb_to_vnd  ) AS cost_china2_vnd'),
            DB::raw(" $selectOtherCosts  AS other_costs "),
            DB::raw(" $_selectOtherCosts  AS other_costs2 "),
            DB::raw(" $__selectOtherCosts  AS other_costs3 "),
            DB::raw(" ( $selectWeight * COALESCE(fare_unit_by_weight, 0) ) AS fare_by_weight "),
            DB::raw(' ( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL), 0) * COALESCE(fare_unit_by_cubic_meters, 0) ) AS fare_by_cubic_meters '),

            DB::raw(' ( COALESCE((SELECT SUM(amount) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL), 0) * COALESCE(fare_unit_by_cubic_meters, 0) ) AS paid'),

            'customers.code AS customer_code',
            'trucks.cost AS truck_cost'
        )
        ->leftJoin('customers', 'orders.customer_id', 'customers.id')
        ->leftJoin('reports', 'orders.id', 'reports.order_id')
        ->leftJoin('trucks', 'orders.truck_id', 'trucks.id')
        ->whereNull('orders.deleted_at')
        ->whereNull('customers.deleted_at')
        ->get()
        ->map(function ($order) {
            $fare = 0;
            $fareByWeight = $order->fare_by_weight;
            $fareByCubicMeters = $order->fare_by_cubic_meters;
            if ($fareByWeight > $fareByCubicMeters) {
                $fare = $fareByWeight;
            } else {
                $fare = $fareByCubicMeters;
            }

            $order->fare = $fare;
            $order->revenue = $order->fare + $order->taxes + $order->other_costs;
            $order->debt = $order->revenue - $order->paid;

            $order->truck_total_cost = collect(json_decode($order->truck_cost, true))->flatten()->sum(function ($item) {
                return is_numeric($item) && $item > 100 ? $item : 0;
            });

            $order->report_cost = $order->taxes + $order->cost_china_vnd + $order->other_costs2;
            $order->report_sale_revenue = 0.04 * $order->revenue - $order->other_costs2 - $order->cost_china2_vnd;
            $order->report_net_income = $order->revenue - $order->report_sale_revenue - $order->report_cost;

            return $order;
        });
}

function getOrders2($truckId = null, $departureMonth = null, $departureYear = null)
{
    $query = DB::table('orders');

    if ($truckId) {
        if (is_array($truckId)) {
            $query = $query
                ->whereIn('truck_id', $truckId);
        } else {
            $query = $query
                ->where('truck_id', $truckId);
        }
    }

    if (!is_null($departureMonth)) {
        $query = $query
            ->whereMonth('trucks.departure_date', $departureMonth);
    }

    if (!is_null($departureYear)) {
        $query = $query
            ->whereYear('trucks.departure_date', $departureYear);
    }

    $selectWeightFromPacks = 'COALESCE((SELECT SUM(weight * quantity) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL), 0)';
    $selectWeightFromColumn = 'COALESCE(weight, 0)';
    $selectWeight = "IF($selectWeightFromPacks > $selectWeightFromColumn, $selectWeightFromPacks, $selectWeightFromColumn)";

    $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';
    $_selectOtherCosts = " IF( other_costs IS NULL, $selectOtherCosts, other_costs) ";
    $__selectOtherCosts = " IF( other_costs IS NULL, 0, other_costs) ";

    return $query
        ->select(
            'orders.id',
            'orders.code',
            'customer_id',
            'truck_id',

            'fare_unit_by_weight',
            'fare_unit_by_cubic_meters',

            'weight',
            DB::raw(' (SELECT SUM(packs.weight * packs.quantity) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL) AS weight_by_packs '),
            DB::raw(' (SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL) AS cubic_meters '),

            'cost_china',
            'cost_china1',
            'cost_china2',
            'cost_vietnam',

            'cost_per_weight',
            'cost_per_cubic_meters',

            'rmb_to_vnd',

            DB::raw('COALESCE(report_taxes1, 0) + COALESCE(report_taxes2, 0) AS taxes'),
            'taxes1',
            'taxes2',
            'report_taxes1',
            'report_taxes2',

            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd ) AS cost_china_vnd'),
            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd  ) AS cost_china1_vnd'),
            DB::raw(' ( COALESCE(cost_china2, 0) * rmb_to_vnd  ) AS cost_china2_vnd'),
            DB::raw(" $selectOtherCosts  AS other_costs "),
            DB::raw(" $_selectOtherCosts  AS other_costs2 "),
            DB::raw(" $__selectOtherCosts  AS other_costs3 "),
            DB::raw(" ( $selectWeight * COALESCE(fare_unit_by_weight, 0) ) AS fare_by_weight "),
            DB::raw(' ( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL), 0) * COALESCE(fare_unit_by_cubic_meters, 0) ) AS fare_by_cubic_meters '),

            DB::raw(' ( COALESCE((SELECT SUM(amount) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL), 0) * COALESCE(fare_unit_by_cubic_meters, 0) ) AS paid'),

            'customers.code AS customer_code',
            'trucks.cost AS truck_cost'
        )
        ->leftJoin('customers', 'orders.customer_id', 'customers.id')
        ->leftJoin('reports', 'orders.id', 'reports.order_id')
        ->leftJoin('trucks', 'orders.truck_id', 'trucks.id')
        ->whereNull('orders.deleted_at')
        ->whereNull('customers.deleted_at')
        ->get()
        ->map(function ($order) {
            $fare = 0;
            $fareByWeight = $order->fare_by_weight;
            $fareByCubicMeters = $order->fare_by_cubic_meters;
            if ($fareByWeight > $fareByCubicMeters) {
                $fare = $fareByWeight;
            } else {
                $fare = $fareByCubicMeters;
            }

            $order->fare = $fare;
            $order->revenue = $order->fare + $order->taxes + $order->other_costs;
            $order->debt = $order->revenue - $order->paid;

            $order->truck_total_cost = collect(json_decode($order->truck_cost, true))->flatten()->sum(function ($item) {
                return is_numeric($item) && $item > 100 ? $item : 0;
            });

            $order->report_cost = $order->taxes + $order->cost_china_vnd + $order->other_costs2;
            $order->report_sale_revenue = 0.04 * $order->revenue - $order->other_costs2 - $order->cost_china2_vnd;
            $order->report_net_income = $order->revenue - $order->report_sale_revenue - $order->report_cost;

            return $order;
        });
}

function getTotalDebt($truckId = null, $departureMonth = null, $departureYear = null)
{
    $query = DB::table('orders');

    if ($truckId) {
        if (is_array($truckId)) {
            $query = $query
                ->whereIn('truck_id', $truckId);
        } else {
            $query = $query
                ->where('truck_id', $truckId);
        }
    }

    if (!is_null($departureMonth)) {
        $query = $query
            ->whereMonth('trucks.departure_date', $departureMonth);
    }

    if (!is_null($departureYear)) {
        $query = $query
            ->whereYear('trucks.departure_date', $departureYear);
    }

    return $query
        ->select(
            'orders.id',
            'orders.code',
            'customer_id',
            'truck_id',
            'fare_unit_by_weight',
            'fare_unit_by_cubic_meters',
            'weight',
            DB::raw(' (SELECT SUM(packs.weight * packs.quantity) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL) AS weight_by_packs '),
            DB::raw(' (SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL) AS cubic_meters '),
            'cost_china',
            'cost_china1',
            'cost_china2',
            'cost_vietnam',
            'rmb_to_vnd',
            'taxes',
            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd ) AS cost_china_vnd'),
            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd  ) AS cost_china1_vnd'),
            DB::raw(' ( COALESCE(cost_china2, 0) * rmb_to_vnd  ) AS cost_china2_vnd'),
            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) ) AS other_costs'),
            DB::raw(' ( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) ) AS fare_by_weight'),
            DB::raw(' ( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL), 0) * COALESCE(fare_unit_by_cubic_meters, 0) ) AS fare_by_cubic_meters'),

            DB::raw(' ( COALESCE((SELECT SUM(amount) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL), 0) * COALESCE(fare_unit_by_cubic_meters, 0) ) AS paid'),

            'reports.tax',
            'reports.other_cost',

            'customers.code AS customer_code',

            'trucks.cost AS truck_cost'
        )
        ->leftJoin('customers', 'orders.customer_id', 'customers.id')
        ->leftJoin('reports', 'orders.id', 'reports.order_id')
        ->leftJoin('trucks', 'orders.truck_id', 'trucks.id')
        ->whereNull('orders.deleted_at')
        ->whereNull('customers.deleted_at')
        ->whereNotNull('customers.id')
        ->get()
        ->map(function ($order) {
            $fare = 0;
            $fareByWeight = $order->fare_by_weight;
            $fareByCubicMeters = $order->fare_by_cubic_meters;
            if ($fareByWeight > $fareByCubicMeters) {
                $fare = $fareByWeight;
            } else {
                $fare = $fareByCubicMeters;
            }

            $order->fare = $fare;
            $order->revenue = $order->fare + $order->taxes + $order->other_costs;
            $order->total_costs = $order->fare_by_cubic_meters + $order->taxes + $order->other_costs;
            $order->debt = $order->revenue - $order->paid;

            $order->truck_total_cost = collect(json_decode($order->truck_cost, true))->flatten()->sum(function ($item) {
                return is_numeric($item) && $item > 100 ? $item : 0;
            });

            // $tax = $order->taxes;
            // $otherCost = $order->other_costs;
            $tax = $order->tax;
            $otherCost = $order->other_cost;

            $order->report_cost = $tax + $order->cost_china_vnd + $otherCost;
            $order->report_sale_revenue = 0.04 * $order->revenue - $otherCost - $order->cost_china2_vnd;
            $order->report_net_income = $order->revenue - $order->report_sale_revenue - $order->report_cost;

            return $order;
        })->filter(function ($order) {
            return $order->debt > 0;
        })->sum('debt');
}

function getTotalDebtV2($truckId = null, $departureMonth = null, $departureYear = null)
{
    $query = DB::table('view_orders');

    if ($truckId) {
        if (is_array($truckId)) {
            $query = $query
                ->whereIn('truck_id', $truckId);
        } else {
            $query = $query
                ->where('truck_id', $truckId);
        }
    }

    if (!is_null($departureMonth)) {
        $query = $query
            ->whereMonth('trucks.departure_date', $departureMonth);
    }

    if (!is_null($departureYear)) {
        $query = $query
            ->whereYear('trucks.departure_date', $departureYear);
    }

    return $query
        ->leftJoin('customers', 'view_orders.customer_id', 'customers.id')
        ->leftJoin('trucks', 'view_orders.truck_id', 'trucks.id')
        ->whereNull('view_orders.deleted_at')
        ->whereNull('customers.deleted_at')
        ->whereNotNull('customers.id')
        ->sum('debt');
}

function getOrdersByCustomerId($customerId = null)
{
    $query = DB::table('orders');

    if ($customerId) {
        $query = $query
            ->where('customer_id', $customerId);
    }

    $selectCubicMetersFromPacks = 'COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0)';
    $selectWeightFromPacks = 'COALESCE((SELECT SUM(weight * quantity) FROM packs WHERE orders.id = packs.order_id), 0)';
    $selectWeightFromColumn = 'COALESCE(weight, 0)';
    $selectWeight = "IF($selectWeightFromPacks > $selectWeightFromColumn, $selectWeightFromPacks, $selectWeightFromColumn)";

    $selectOtherCosts = '( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) )';

    $selectFareByWeight = "( $selectWeight * COALESCE(fare_unit_by_weight, 0) )";
    $selectFareByCubicMeters = "( $selectCubicMetersFromPacks * COALESCE(fare_unit_by_cubic_meters, 0) )";

    $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
    $selectRevenue = "IFNULL($selectFare, 0) + IFNULL(taxes, 0) + IFNULL($selectOtherCosts, 0)";
    $selectPaid = 'IFNULL((SELECT SUM( IFNULL(payments.amount, 0) ) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL LIMIT 1), 0)';
    $selectDebt = "IFNULL($selectRevenue, 0) - IFNULL($selectPaid, 0)";

    $selectTotalCostsByWeight = " ( $selectFareByWeight + taxes1 + taxes2 + $selectOtherCosts ) ";
    $selectTotalCostsByCubicMeters = " ( $selectFareByCubicMeters + taxes1 + taxes2 + $selectOtherCosts ) ";
    $selectCostPerWeightFromColumn = " cost_per_weight ";
    $selectCostPerCubicMetersFromColumn = " cost_per_cubic_meters ";
    $selectCostPerWeight = " IF( $selectCostPerWeightFromColumn IS NULL, $selectTotalCostsByWeight / $selectWeight, $selectCostPerWeightFromColumn) ";
    $selectCostPerCubicMeters = " IF( $selectCostPerCubicMetersFromColumn IS NULL, $selectTotalCostsByCubicMeters / $selectCubicMetersFromPacks, $selectCostPerCubicMetersFromColumn) ";

    $selectTaxes = " ( taxes1 + taxes2 ) ";

    $query = $query
        ->select(
            'orders.id',
            'orders.code',
            'customer_id',
            'truck_id',
            'fare_unit_by_weight',
            'fare_unit_by_cubic_meters',

            'weight',
            DB::raw("$selectCubicMetersFromPacks AS cubic_meters"),

            DB::raw(' (SELECT SUM(packs.weight * packs.quantity) FROM packs WHERE orders.id = packs.order_id) AS weight_by_packs '),
            DB::raw(' (SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id) AS cubic_meters '),
            'cost_china',
            'cost_china1',
            'cost_china2',
            'cost_vietnam',

            DB::raw(" $selectCostPerWeight AS cost_per_weight "),
            DB::raw(" $selectCostPerCubicMeters AS cost_per_cubic_meters "),

            'rmb_to_vnd',
            'taxes1',
            'taxes2',
            DB::raw(" $selectTaxes AS taxes "),

            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd ) AS cost_china_vnd'),
            DB::raw(' ( COALESCE(cost_china1, 0) * rmb_to_vnd  ) AS cost_china1_vnd'),
            DB::raw(' ( COALESCE(cost_china2, 0) * rmb_to_vnd  ) AS cost_china2_vnd'),
            DB::raw(' @other_costs := ( COALESCE(cost_china1, 0) * rmb_to_vnd + COALESCE(cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) ) AS other_costs'),

            DB::raw(' @fare_by_weight := ( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) ) AS fare_by_weight'),
            DB::raw(' @fare_by_cubic_meters := ( COALESCE((SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id), 0) * COALESCE(fare_unit_by_cubic_meters, 0) ) AS fare_by_cubic_meters'),
            DB::raw(' @fare := IF(@fare_by_weight > @fare_by_cubic_meters, @fare_by_weight, @fare_by_cubic_meters) AS fare '),
            DB::raw(" $selectFare AS fare "),
            DB::raw(' @revenue := IFNULL(@fare, 0) + IFNULL(taxes, 0) + IFNULL(@other_costs, 0) AS revenue'),
            DB::raw(" $selectRevenue AS revenue "),


            DB::raw(' @paid := (SELECT IFNULL(SUM(payments.amount), 0) FROM payments WHERE orders.id = payments.order_id AND payments.deleted_at IS NULL) AS paid '),
            DB::raw(" $selectPaid AS paid "),

            DB::raw(' @debt := IFNULL(@revenue, 0) - IFNULL(@paid, 0) AS debt'),
            DB::raw(" $selectDebt AS debt "),

            'customers.code AS customer_code',
            'customers.phone AS customer_phone',
        )
        ->leftJoin('customers', 'orders.customer_id', 'customers.id')
        ->leftJoin('trucks', 'orders.truck_id', 'trucks.id')
        ->whereNull('orders.deleted_at')
        ->orderByRaw("$selectDebt DESC");

    return $query->get();
}

function filterCustomers($options = [
    'id' => null,
    'user_id' => null,
    'q' => null,
])
{
    // createViewOrders();

    $selectTotalRevenue = "SELECT SUM(IFNULL(revenue, 0)) FROM view_orders WHERE view_orders.deleted_at IS NULL AND view_orders.customer_id = customers.id";
    $selectTotalPaid = " SELECT SUM(IFNULL(paid, 0)) FROM view_orders WHERE view_orders.deleted_at IS NULL AND view_orders.customer_id = customers.id ";
    $selectTotalDebt = " SELECT SUM(IFNULL(revenue, 0) - IFNULL(paid, 0)) FROM view_orders WHERE view_orders.deleted_at IS NULL AND view_orders.customer_id = customers.id ";

    $selectTotalDeposit = " SELECT SUM(amount) FROM transactions WHERE transactions.customer_id = customers.id AND transactions.deleted_at IS NULL AND transactions.type = '" . Transaction::TYPE_DEPOSIT . "' AND transactions.status = " . Transaction::STATUS_TEXT_COMPLETED;
    $selectTotalSpend = " SELECT SUM(amount) FROM transactions WHERE transactions.customer_id = customers.id AND transactions.deleted_at IS NULL AND transactions.type = '" . Transaction::TYPE_PAYMENT . "' AND transactions.status = " . Transaction::STATUS_TEXT_COMPLETED;

    $query = DB::table('customers')
        ->select([
            'customers.id',
            'customers.user_id',
            'customers.code',
            'customers.name',
            'customers.phone',
            'customers.address',
            'customers.balance',

            DB::raw(" ($selectTotalRevenue) AS revenue "),
            DB::raw(" ($selectTotalPaid) AS paid "),
            DB::raw(" ($selectTotalDebt) AS debt "),

            DB::raw(" ($selectTotalDeposit) AS total_deposit "),
            DB::raw(" ($selectTotalSpend) AS total_spend "),
        ]);

    $id = Arr::get($options, 'id');
    $userId = Arr::get($options, 'user_id');
    $q = Arr::get($options, 'q');

    if ($id) {
        $query = $query
            ->where('id', $id);
    }

    if ($userId) {
        $query = $query
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
    }

    $query = $query
        ->orderByRaw("($selectTotalDebt) DESC");

    return $query->paginate();
}

function getTotalDebtCustomers($options = [
    'id' => null,
    'user_id' => null,
    'q' => null,
    'seller_id' => null
])
{
    // createViewOrders();

    $selectTotalRevenue = "SELECT SUM(IFNULL(revenue, 0)) FROM view_orders WHERE view_orders.deleted_at IS NULL AND view_orders.customer_id = customers.id";
    $selectTotalPaid = " SELECT SUM(IFNULL(paid, 0)) FROM view_orders WHERE view_orders.deleted_at IS NULL AND view_orders.customer_id = customers.id ";
    $selectTotalDebt = " SELECT SUM(IFNULL(revenue, 0) - IFNULL(paid, 0)) FROM view_orders WHERE view_orders.deleted_at IS NULL AND view_orders.customer_id = customers.id ";

    $selectTotalDeposit = " SELECT SUM(amount) FROM transactions WHERE transactions.customer_id = customers.id AND transactions.deleted_at IS NULL AND transactions.type = '" . Transaction::TYPE_DEPOSIT . "' AND transactions.status = " . Transaction::STATUS_TEXT_COMPLETED;
    $selectTotalSpend = " SELECT SUM(amount) FROM transactions WHERE transactions.customer_id = customers.id AND transactions.deleted_at IS NULL AND transactions.type = '" . Transaction::TYPE_PAYMENT . "' AND transactions.status = " . Transaction::STATUS_TEXT_COMPLETED;

    $query = DB::table('customers')
        ->select([
            'customers.id',
            'customers.user_id',
            'customers.code',
            'customers.name',
            'customers.phone',
            'customers.address',

            DB::raw(" ($selectTotalRevenue) AS revenue "),
            DB::raw(" ($selectTotalPaid) AS paid "),
            DB::raw(" ($selectTotalDebt) AS debt "),

            DB::raw(" ($selectTotalDeposit) AS total_deposit "),
            DB::raw(" ($selectTotalSpend) AS total_spend "),
        ]);

    $id = Arr::get($options, 'id');
    $userId = Arr::get($options, 'user_id');
    $q = Arr::get($options, 'q');

    if ($id) {
        $query = $query
            ->where('id', $id);
    }

    if ($userId) {
        $query = $query
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
    }

    $query = $query
        ->orderByRaw("($selectTotalDebt) DESC");

    return $query->get()->sum('debt');
}

function getOrdersFromView($truckId)
{
    createViewOrders();

    $truck = Truck::find($truckId);

    return DB::table('view_orders')
        ->select('view_orders.*', 'customers.code AS customer_code')
        ->where('truck_id', $truckId)
        ->whereNull('view_orders.deleted_at')
        ->leftJoin('customers', 'view_orders.customer_id', '=', 'customers.id')
        ->get()
        ->map(function ($order) use ($truck) {
            $order->truck_total_cost = collect($truck->cost, true)->flatten()->sum(function ($item) {
                return is_numeric($item) && $item > 100 ? $item : 0;
            });

            $order->report_cost = $order->taxes + $order->_other_costs;
            $order->report_sale_revenue = 0.04 * $order->revenue - $order->_other_costs;
            $order->report_net_income = $order->revenue - $order->report_sale_revenue - $order->report_cost;

            return $order;
        });
}

function getTotalNetIncome($truckId)
{
    $orders = getOrdersFromView($truckId);
    $cubicMeters = $orders->sum('cubic_meters');
    $weight = $orders->sum('weight');

    $costPerCubicMeters = $cubicMeters == 0 ? 0 : $orders[0]->truck_total_cost / $cubicMeters;
    $costPerWeight = $weight == 0 ? 0 : $orders[0]->truck_total_cost / $weight;

    $orders->map(function ($order) use ($costPerCubicMeters, $costPerWeight, $weight, $cubicMeters) {
        $costPerCubicMeters = $cubicMeters == 0 ? 0 : $order->truck_total_cost / $cubicMeters;
        $costPerWeight = $weight == 0 ? 0 : $order->truck_total_cost / $weight;
        if (!is_null($order->cost_per_cubic_meters)) {
            $costPerCubicMeters = $order->cost_per_cubic_meters;
        }

        if (!is_null($order->cost_per_weight)) {
            $costPerWeight = $order->cost_per_weight;
        }

        $totalCostsByCubicMeters = $costPerCubicMeters * $order->cubic_meters;
        $totalCostsByWeight = $costPerWeight * $order->weight;
        $totalCosts = $totalCostsByCubicMeters > $totalCostsByWeight ? $totalCostsByCubicMeters : $totalCostsByWeight;
        $totalCosts += $order->report_taxes1 + $order->report_taxes2 + $order->cost_china1_vnd + $order->cost_china2_vnd;

        $saleRevenue = 0.04 * ($order->revenue - $order->report_other_cost - $order->cost_china1_vnd - $order->cost_china2_vnd);

        $netIncome = $order->revenue - $totalCosts - $saleRevenue;

        $order->netIncome = $netIncome;

        return $order;
    });

    return $orders->sum('netIncome');
}

function createViewOrders()
{
    $selectOtherCosts = ' COALESCE(cost_china1 + cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) ';
    $selectCostChina1Vnd = ' COALESCE(cost_china1, 0) * rmb_to_vnd ';
    $selectCostChina2Vnd = ' COALESCE(cost_china2, 0) * rmb_to_vnd ';
    $selectCubicMeters = ' ( SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL ) ';

    $selectFareByWeight = " ( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) ) ";
    $selectFareByWeightFromPacks = " ( SELECT SUM(packs.weight * packs.quantity) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ) * COALESCE(fare_unit_by_weight, 0) ";
    $selectFareByWeight = " IF($selectFareByWeight > $selectFareByWeightFromPacks, $selectFareByWeight, $selectFareByWeightFromPacks) ";
    $selectFareByCubicMeters = " ( SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL ) * COALESCE(fare_unit_by_cubic_meters, 0) ";
    $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
    $selectTaxes = "IFNULL(taxes1, 0) + IFNULL(taxes2, 0)";

    $selectPaid = " SELECT IFNULL(SUM(payments.amount), 0) FROM payments WHERE payments.order_id = orders.id AND payments.deleted_at IS NULL ";
    $selectRevenue = "$selectFare + $selectTaxes + $selectOtherCosts";
    $selectDebt = " ( SELECT $selectFare + $selectTaxes + $selectOtherCosts ) - ($selectPaid)";

    $sql = "
    CREATE OR REPLACE VIEW view_orders AS

    SELECT 
        *,
        ( SELECT $selectOtherCosts ) AS _other_costs,
        ( SELECT $selectOtherCosts ) AS other_costs3,
        ( SELECT $selectCostChina1Vnd ) AS cost_china1_vnd,
        ( SELECT $selectCostChina2Vnd ) AS cost_china2_vnd,
        ( SELECT $selectCubicMeters ) AS cubic_meters,
        ( SELECT $selectTaxes ) AS _taxes,
        ( SELECT $selectFare ) AS fare,
        ( SELECT $selectRevenue ) AS revenue,
        ( $selectPaid ) AS paid,
        ( $selectDebt ) AS debt

    FROM orders
    ";

    return DB::select(DB::raw($sql));
}

function createViewTotalDebt()
{
    $selectOtherCosts = ' COALESCE(cost_china1 + cost_china2, 0) * rmb_to_vnd + COALESCE(cost_vietnam, 0) ';
    $selectCostChina1Vnd = ' COALESCE(cost_china1, 0) * rmb_to_vnd ';
    $selectCostChina2Vnd = ' COALESCE(cost_china2, 0) * rmb_to_vnd ';
    $selectCubicMeters = ' ( SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL ) ';

    $selectFareByWeight = " ( COALESCE(weight, 0) * COALESCE(fare_unit_by_weight, 0) ) ";
    $selectFareByWeightFromPacks = " ( SELECT SUM(packs.weight * packs.quantity) FROM packs WHERE packs.deleted_at IS NULL AND packs.order_id = orders.id ) * COALESCE(fare_unit_by_weight, 0) ";
    $selectFareByWeight = " IF($selectFareByWeight > $selectFareByWeightFromPacks, $selectFareByWeight, $selectFareByWeightFromPacks) ";
    $selectFareByCubicMeters = " ( SELECT SUM(height * width * depth * quantity / 1000000) FROM packs WHERE orders.id = packs.order_id AND packs.deleted_at IS NULL ) * COALESCE(fare_unit_by_cubic_meters, 0) ";
    $selectFare = "IF($selectFareByWeight > $selectFareByCubicMeters, $selectFareByWeight, $selectFareByCubicMeters)";
    $selectTaxes = "IFNULL(taxes1, 0) + IFNULL(taxes2, 0)";

    $selectPaid = " SELECT IFNULL(SUM(payments.amount), 0) FROM payments WHERE payments.order_id = orders.id AND payments.deleted_at IS NULL ";
    $selectRevenue = "$selectFare + $selectTaxes + $selectOtherCosts";
    $selectDebt = " ( SELECT $selectFare + $selectTaxes + $selectOtherCosts ) - ($selectPaid)";
    $selectTotalDebt = " SELECT SUM ( $selectFare + $selectTaxes + $selectOtherCosts - ($selectPaid) ) ";

    $sql = "
    CREATE OR REPLACE VIEW view_total_debt AS

    SELECT 
        *,
        ( $selectFare ) AS fare
    FROM orders
    ";

    return DB::select(DB::raw($sql));
}

function createViewOrders2()
{
    // $sql = "
    // CREATE OR REPLACE VIEW view_orders2 AS

    // SELECT 
    //     -- orders.*, 
    //     MIN(orders.id) AS id,
    //     MIN(orders.customer_id) AS customer_id,
    //     MIN(orders.truck_id) AS truck_id,
    //     MIN(orders.code) AS code,
    //     MIN(orders.bill) AS bill,
    //     SUM(payments.amount) AS paid,
    //     COUNT(payments.id) AS count_payments
    // FROM orders
    // LEFT JOIN packs ON orders.id = packs.order_id
    // LEFT JOIN payments ON orders.id = payments.order_id
    // WHERE 
    //     orders.deleted_at IS NULL AND packs.deleted_at IS NULL AND 
    //     payments.deleted_at IS NULL
    // GROUP BY orders.id
    // ORDER BY orders.id
    // ";

    $sql = "
    CREATE OR REPLACE VIEW view_orders2 AS

    SELECT 
        -- COUNT(payments.id) AS count_payments
        orders.*
    FROM orders
    LEFT JOIN packs ON orders.id = packs.order_id
    -- LEFT JOIN payments ON orders.id = payments.order_id
    WHERE 
        orders.deleted_at IS NULL AND packs.deleted_at IS NULL 
        -- AND 
        -- payments.deleted_at IS NULL AND orders.id = 3
    ";


    // $sql = "
    // CREATE OR REPLACE VIEW view_orders2 AS

    // SELECT 
    //     MIN(o.id) AS id,
    //     MIN(o.customer_id) AS customer_id,
    //     MIN(o.truck_id) AS truck_id,
    //     MIN(o.code) AS code,
    //     MIN(o.bill) AS bill,
    //     MIN(o.product_name) AS product_name,
    //     MIN(o.images) AS images,
    //     MIN(o.weight) AS weight,
    //     MIN(o.taxes) AS taxes,
    //     MIN(o.taxes1) AS taxes1,
    //     MIN(o.taxes2) AS taxes2,
    //     SUM(payments.amount) AS paid
    // FROM 

    // (
    //     SELECT 
    //         MIN(orders.id) AS id,
    //         MIN(orders.customer_id) AS customer_id,
    //         MIN(orders.truck_id) AS truck_id,
    //         MIN(orders.code) AS code,
    //         MIN(orders.bill) AS bill,
    //         MIN(orders.product_name) AS product_name,
    //         MIN(orders.images) AS images,
    //         MIN(orders.weight) AS weight,
    //         MIN(orders.taxes) AS taxes,
    //         MIN(orders.taxes1) AS taxes1,
    //         MIN(orders.taxes2) AS taxes2
    //     FROM orders
    //     LEFT JOIN packs ON orders.id = packs.order_id
    //     WHERE orders.deleted_at IS NULL AND packs.deleted_at IS NULL
    //     GROUP BY orders.id
    // ) AS o

    // LEFT JOIN payments ON o.id = payments.order_id
    // WHERE payments.deleted_at IS NULL
    // GROUP BY o.id
    // ORDER BY o.id
    // ";

    return DB::select(DB::raw($sql));
}

function formatAddress($address)
{
    $address = trim($address);

    if (strlen($address) > 10) return substr($address, 0, 10) . '...';

    return $address;
}

function isAdmin()
{
    if (!auth()->check()) {
        return false;
    }

    return auth()->user()->is_admin;
}

function isSeller()
{
    if (!auth()->check()) {
        return false;
    }

    return auth()->user()->is_seller;
}

function expandKhoBangTuongMenu()
{
    $routeName = request()->route()->getName();

    return
        $routeName === 'order.noname' ||
        $routeName === 'order.index' ||
        $routeName === 'cost_china.index' ||
        $routeName === 'order.express';
}

function expandLocationMenu(Location $location)
{
    $route = request()->route();

    $name = $route->getName();

    if ($name === 'order.location') {
        return optional($route->parameter('location'))->id === $location->id;
    }

    return
        $name === 'order.noname' ||
        $name === 'cost_china.index' ||
        $name === 'order.express';
}

function expandVnInventoryMenu(Location $location)
{
    $route = request()->route();

    $name = $route->getName();

    return $name === 'order.vietnamese_inventory.location' && optional($route->parameter('location'))->id === $location->id;
}

function expandUnpaidMenu(Location $location)
{
    $route = request()->route();

    $name = $route->getName();

    return
        $name === 'order.unpaid' &&
        request()->get('location_id') == $location->id;
}

function genQRQuickLink(
    $amount,
    $memo,
    $bank = '970407',
    $accountName = 'LAM XUAN DONG',
    $accountNumber = '19038769553019'
) {
    return "https://api.vietqr.io/{$bank}/{$accountNumber}/{$amount}/{$memo}/compact.jpg?accountName={$accountName}";
}

function genTransactionDescription($customer)
{
    $customerCode = str_replace('-', '', optional($customer)->code);
    $id = optional($customer)->id;

    return "{$id}ID{$customerCode}";
}

function parseTransactionDescription($description)
{
    $splitted = explode('ID', $description);

    return [
        'id' => $splitted[0],
        'code' => $splitted[1],
    ];
}

function showDeleteCustomerBtn()
{
    if (!auth()->check()) {
        return false;
    }

    $role = auth()->user()->role;

    return $role !== User::ROLE_VN_INVENTORY && $role !== User::ROLE_CN_INVENTORY && $role !== User::ROLE_ACCOUNTANT;
}

function getAvatarUrl()
{
    if (auth()->check()) {
        return auth()->user()->avatar_url;
    }

    if (auth('customer')->check()) {
        return auth('customer')->user()->avatar_url;
    }
}
