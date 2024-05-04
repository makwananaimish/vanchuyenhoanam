<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Traits\LogsActivity;

class Truck extends Model
{
    use SoftDeletes, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'shipping_method_id',
        'departure_location_id',
        'current_location_id',
        'departure_date',
        'arrival_date',
        'cost',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'cost' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'cost_per_cubic_meters',

        // 'revenue',
        // 'total_cost',
        // 'profit',

        // 'debt',

        // 'cubic_meters',
        // 'weight',

        // 'report_revenue',
        // 'report_net_income',

        'arrival_date_formatted'
    ];

    protected static $logAttributes = [
        'name',
        'departure_date',
        'arrival_date',

        'departureLocation.name',
        'currentLocation.name',
    ];

    public function departureLocation()
    {
        return $this->belongsTo(Location::class, 'departure_location_id');
    }

    public function currentLocation()
    {
        return $this->belongsTo(Location::class, 'current_location_id');
    }

    public function customers()
    {
        return $this->belongsToMany(Customer::class, 'orders');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function getCostPerCubicMetersAttribute()
    {
        if ($this->cubic_meters == 0) return 0;

        return $this->total_cost / $this->cubic_meters;
    }

    public function getRevenueAttribute()
    {
        return $this->orders->sum('revenue');
    }

    public function getTotalCostAttribute()
    {
        return collect($this->cost)->flatten()->sum(function ($item) {
            return is_numeric($item) && $item > 100 ? $item : 0;
        });
    }

    public function getProfitAttribute()
    {
        return $this->revenue - $this->total_cost;
    }

    public function getDebtAttribute()
    {
        return $this->orders->sum('debt');
    }

    public function getCubicMetersAttribute()
    {
        return $this->orders->sum('cubic_meters');
    }

    public function getWeightAttribute()
    {
        return $this->orders->sum('weight');
    }

    public function getReportRevenueAttribute()
    {
        return $this->orders->sum('customer.revenue');
    }

    public function getReportNetIncomeAttribute()
    {
        return $this->orders->sum(function ($order) {
            $tax = optional($order->report)->tax;
            $otherCost = optional($order->report)->other_cost;

            $cost = $order->fare_by_cubic_meters + $tax + $order->cost_china_vnd + $otherCost;
            $saleRevenue = 0.04 * optional($order->customer)->revenue - $otherCost - $order->fare_by_cubic_meters;
            $netIncome = optional($order->customer)->revenue - $saleRevenue - $cost;

            return $netIncome;
        });
    }

    public function updateCostPerCubicMetersOfAllOrders()
    {
        $costPerCubicMeters = $this->cubic_meters == 0 ? 0 : $this->total_cost / $this->cubic_meters;

        return DB::table('orders')
            ->whereNull('deleted_at')
            ->where('truck_id', $this->id)
            ->update([
                'cost_per_cubic_meters' => $costPerCubicMeters
            ]);
    }

    public function updateCostPerWeightOfAllOrders()
    {
        $costPerWeight = $this->weight == 0 ? 0 : $this->total_cost / $this->weight;

        return DB::table('orders')
            ->whereNull('deleted_at')
            ->where('truck_id', $this->id)
            ->update([
                'cost_per_weight' => $costPerWeight
            ]);
    }

    public function updateInVietnameseInventoryDateOfAllOrders()
    {
        return DB::table('orders')
            ->whereNull('deleted_at')
            ->where('truck_id', $this->id)
            ->update([
                'in_vietnamese_inventory_date' => Carbon::now()
            ]);
    }

    public function atTransshipmentPointAttribute()
    {
        return optional($this->currentLocation)->type === Location::TRANSSHIPMENT;
    }

    public function getArrivalDateFormattedAttribute()
    {
        try {
            return Carbon::createFromFormat('Y-m-d H:i:s', $this->arrival_date)->format('d-m-Y');
        } catch (\Throwable $th) {
            return null;
        }
    }
}
