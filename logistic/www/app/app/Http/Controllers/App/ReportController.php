<?php

namespace App\Http\Controllers\App;

use App\Location;
use App\Order;
use App\ShippingMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportController extends BaseController
{
    public function index()
    {
        $fromMonth = request('from_month');
        $toMonth = request('to_month');
        $departureLocationId = request('departure_location_id');
        $truckId = request('truck_id');
        $truckName = request('truck_name');
        $shippingMethodId = request('shipping_method_id');

        $query = DB::table('trucks');

        if (!is_null($fromMonth)) {
            $_fromMonth = Carbon::createFromFormat('Y-m', $fromMonth);
            $query = $query->whereMonth('trucks.created_at', '>=', $_fromMonth->month);
            $query = $query->whereYear('trucks.created_at', '>=', $_fromMonth->year);
        }

        if (!is_null($toMonth)) {
            $_toMonth = Carbon::createFromFormat('Y-m', $toMonth);
            $query = $query->whereMonth('trucks.created_at', '<=', $_toMonth->month);
            $query = $query->whereYear('trucks.created_at', '<=', $_toMonth->year);
        }

        if (!is_null($departureLocationId)) {
            $query = $query->where('departure_locations.id', $departureLocationId);
        }

        if (!is_null($truckId)) {
            $query = $query->where('trucks.id', $truckId);
        }

        if (!is_null($truckName)) {
            $query = $query->where('trucks.name', $truckName);
        }

        if (!is_null($shippingMethodId)) {
            $query = $query->where('shipping_method_id', $shippingMethodId);
        }

        $trucks = $query
            ->select([
                'trucks.*',
                'departure_locations.name AS departure_location',
                'current_locations.name AS current_location',
            ])
            ->whereNull('trucks.deleted_at')
            // ->where('trucks.id', 1)
            ->leftJoin('locations AS departure_locations', 'trucks.departure_location_id', 'departure_locations.id')
            ->leftJoin('locations AS current_locations', 'trucks.current_location_id', 'current_locations.id')
            ->paginate();

        $locations = Location::all();
        $shippingMethods = ShippingMethod::all();

        return view('app.report.index', [
            'trucks' => $trucks,
            'locations' => $locations,
            'shippingMethods' => $shippingMethods,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->all();

        $taxes1 = $data['taxes1'];
        $taxes2 = $data['taxes2'];
        $otherCost = $data['other_cost'];
        $costPerWeight = $data['cost_per_weight'];
        $costPerCubicMeters = $data['cost_per_cubic_meters'];

        foreach ($taxes1 as $orderId => $tax) {
            $order = Order::where('id', $orderId)->first();

            if (!is_null($order)) {
                $order->fill([
                    'report_taxes1' => $tax,
                    'report_taxes2' => $taxes2[$orderId],
                    'report_other_cost' => $otherCost[$orderId],
                    'cost_per_weight' => $costPerWeight[$orderId],
                    'cost_per_cubic_meters' => $costPerCubicMeters[$orderId],
                ]);
                $order->save();
            } else {
                Log::info("Cannot find order id #{$orderId}");
            }
        }

        return redirect()->back()->withInput();
    }
}
