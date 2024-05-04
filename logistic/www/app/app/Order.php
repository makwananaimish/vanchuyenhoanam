<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;

class Order extends Model
{
    use SoftDeletes, LogsActivity;

    public const STATUS_TEXT_COMPLETED = 'Hoàn thành';
    public const STATUS_TEXT_WAIT_FOR_PAYING = 'Đợi thanh toán';
    public const STATUS_TEXT_UNDELIVERED = 'Đơn chưa giao';
    public const STATUS_TEXT_DELIVERED = 'Đã giao';
    public const STATUS_TEXT_IS_NOT_CALCULATED_COST = 'Chưa tính tiền';

    public const STATUS_TEXT_IS_RECEIVED_IN_CHINA = 'Đã nhận hàng bên Kho TQ';
    public const STATUS_TEXT_ON_TRUCK = 'Đã xếp xe';
    public const STATUS_TEXT_IN_VIETNAM = 'Đã về Việt Nam';
    public const STATUS_TEXT_NONAME = 'Hàng không tên';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location_id',
        'customer_id',
        'truck_id',
        'code',
        'bill',
        'product_name',
        'image',
        'images',
        'weight',

        'taxes',
        'taxes1',
        'taxes2',

        'cost_china',
        'cost_china1',
        'cost_china2',
        'cost_vietnam',

        'other_costs',

        'cost_per_weight',
        'cost_per_cubic_meters',

        'fare_unit_by_weight',
        'fare_unit_by_cubic_meters',

        'rmb_to_vnd',

        'note',
        'note_in_list',
        'note_in_vn_inventory',
        'note_in_truck',

        'added_to_truck_at',
        'in_vietnamese_inventory_date',
        'delivery_date',
        'driver_phone',
        'license_plate_number',

        'report_taxes1',
        'report_taxes2',
        'report_other_cost',

        'status'
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'formatted_bill',

        // 'weight',
        // 'cubic_meters',

        // 'fare',
        // 'fare_by_weight',
        // 'fare_by_cubic_meters',

        // 'other_costs',
        // 'cost_china_vnd',
        // 'cost_china1_vnd',
        // 'cost_china2_vnd',
        // 'total_costs',
        // 'revenue',
        // 'report_revenue',
        // 'profit',

        // 'is_calculated_cost',

        // 'paid',
        // 'debt',

        // 'can_delivery',
        // 'status',
        // 'status_text',

        // 'cost_against_outcome',

        // 'report_cost',
        // 'report_sale_revenue',
        // 'report_net_income',

        // 'is_express',

        // 'sale_taxes',
        // 'sale_net_income',
        // 'sale_revenue',
        // 'sale_commission',

        // 'is_declared',

        // 'created_at_format',
        // 'delivery_date_format',
        // 'arrival_date_format',

        'is_notified_address'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
    ];

    protected static $logAttributes = [
        'code',
        'bill',
        'product_name',
        'weight',

        'taxes',
        'taxes1',
        'taxes2',

        'cost_china',
        'cost_china1',
        'cost_china2',
        'cost_vietnam',

        'note',

        'truck.name',
        'customer.name',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function truck()
    {
        return $this->belongsTo(Truck::class);
    }

    public function report()
    {
        return $this->hasOne(Report::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function declarations()
    {
        return $this->hasMany(OrderDeclaration::class);
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'order_transactions');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function packs()
    {
        return $this->hasMany(Pack::class);
    }

    public function getFormattedBillAttribute()
    {
        $value = $this->bill;

        if (Str::length($value) <= 8) return $value;

        return substr($value, 0, 8) . '...';
    }

    public function getIsExpressAttribute()
    {
        return optional($this->customer)->code === Customer::EXPRESS_CODE;
    }

    public function getWeightAttribute($value)
    {
        // $totalPacksWeight = DB::table('packs')
        //     ->where('order_id', $this->id)
        //     ->whereNull('deleted_at')
        //     ->sum(DB::raw('weight * quantity'));

        $totalPacksWeight = $this->packs->sum(function ($pack) {
            return $pack->weight * $pack->quantity;
        });

        if ($value) {
            return $value > $totalPacksWeight ? $value : $totalPacksWeight;
        }

        return $totalPacksWeight;
    }

    public function getTaxesAttribute()
    {
        return $this->taxes1 + $this->taxes2;
    }

    public function getCubicMetersAttribute()
    {
        // return DB::table('packs')
        //     ->where('order_id', $this->id)
        //     ->whereNull('deleted_at')
        //     ->sum(DB::raw('height * width * depth * quantity / 1000000'));

        return
            $this->packs->sum(function ($pack) {
                return $pack->height * $pack->width * $pack->depth * $pack->quantity / 1000000;
            });
    }

    public function getFareAttribute()
    {
        $fareByWeight = $this->fare_by_weight;
        $fareByCubicMeters = $this->fare_by_cubic_meters;

        if ($fareByWeight > $fareByCubicMeters)  return $fareByWeight;

        return $fareByCubicMeters;
    }

    public function getFareByWeightAttribute()
    {
        return $this->weight * $this->fare_unit_by_weight;
    }

    public function getFareByCubicMetersAttribute()
    {
        return $this->cubic_meters * $this->fare_unit_by_cubic_meters;
    }

    public function getOtherCostsAttribute()
    {
        // return is_null($this->getOriginal('other_costs')) ? $this->cost_china * (int)$this->rmb_to_vnd + $this->cost_vietnam : $this->getOriginal('other_costs');

        return $this->cost_china * (int)$this->rmb_to_vnd + $this->cost_vietnam;
    }

    public function getCostChinaAttribute($value)
    {
        return $this->cost_china1 + $this->cost_china2;
    }

    public function getRmbToVNDAttribute($value)
    {
        if (is_null($value)) {
            //     $rmbToVnd = Cache::get('rmb_to_vnd');

            //     if (is_null($rmbToVnd)) {

            $rmbToVnd = Option::where('name', 'rmb_to_vnd')->first()->value;

            //         Cache::forever('rmb_to_vnd', $rmbToVnd);
            //     }

            return $rmbToVnd;
        }

        return $value;
    }

    public function getCostChinaVndAttribute()
    {
        return $this->cost_china * (int)$this->rmb_to_vnd;
    }

    public function getCostChina1VndAttribute()
    {
        return $this->cost_china1 * (int)$this->rmb_to_vnd;
    }

    public function getCostChina2VndAttribute()
    {
        return $this->cost_china2 * (int)$this->rmb_to_vnd;
    }

    public function getTotalCostsAttribute()
    {
        return $this->fare_by_cubic_meters + $this->taxes + $this->other_costs;
    }

    public function getRevenueAttribute()
    {
        return $this->fare + $this->taxes + $this->other_costs;
    }

    public function getReportRevenueAttribute()
    {
        return $this->fare + $this->report_taxes1 + $this->report_taxes2 + $this->other_costs;
    }

    public function getProfitAttribute()
    {
        return $this->revenue - $this->total_costs;
    }

    public function getPaidAttribute()
    {
        return $this->payments->sum('amount');
    }

    public function getDebtAttribute()
    {
        $value =  $this->revenue - $this->paid;

        if ($value > -1000 && $value <= 0) {
            return 0;
        }

        return $value;
    }

    public function getCanDeliveryAttribute()
    {
        $currentLocation = optional($this->truck)->currentLocation;

        // return optional($currentLocation)->name === Location::VIETNAM_INVENTORY;
        return optional($currentLocation)->in_vn;
    }

    public function getStatusAttribute()
    {
        if ($this->packs->count() === 0)
            return (int) $this->getOriginal('status', Pack::IN_PROGRESS);

        if ($this->packs->where('status', Pack::DELIVERED)->count() === $this->packs->count())
            return Pack::DELIVERED;

        return Pack::IN_PROGRESS;
    }

    public function getIsCalculatedCostAttribute()
    {
        return $this->taxes1 > 0 || $this->taxes2 > 0 || $this->cost_vietnam > 0 || $this->fare_unit_by_weight > 0 || $this->fare_unit_by_cubic_meters > 0;
    }

    public function getStatusTextAttribute()
    {
        if (is_null($this->truck)) {
            return self::STATUS_TEXT_IS_RECEIVED_IN_CHINA;
        }

        if ($this->status === Pack::DELIVERED) {
            if ($this->is_calculated_cost) {
                if ($this->debt <= 1) {
                    return self::STATUS_TEXT_COMPLETED;
                } else {
                    return self::STATUS_TEXT_WAIT_FOR_PAYING;
                }
            } else {
                return self::STATUS_TEXT_IS_NOT_CALCULATED_COST;
            }
        } else {
            return self::STATUS_TEXT_UNDELIVERED;
        }
    }

    public function getReportCostAttribute()
    {
        $tax = optional($this->report)->tax;
        $otherCost = optional($this->report)->other_cost;
        return $this->fare_by_cubic_meters + $tax + $this->cost_china_vnd + $otherCost;
    }

    public function getReportSaleRevenueAttribute()
    {
        $otherCost = optional($this->report)->other_cost;
        return 0.04 * $this->customer->revenue - $otherCost - $this->fare_by_cubic_meters;
    }

    public function getReportNetIncomeAttribute()
    {
        return $this->customer->revenue - $this->report_sale_revenue - $this->report_cost;
    }

    public function getCommissionByWeightAttribute()
    {

        // $outcomeWeight = (int)optional(Option::where('name', 'outcome_weight')->first())->value;

        // if ($this->cubic_meters == 0) {
        //     return 0;
        // }

        // $rate = $this->weight / $this->cubic_meters;

        // if (
        //     $this->weight < 1000
        // ) {
        //     return $this->taxes + ($outcomeWeight  / $rate) * $this->weight + 3000 * $this->weight;
        // } elseif ($this->weight >= 1000 && $this->weight < 3000) {
        //     return $this->taxes + ($outcomeWeight  / $rate) * $this->weight + 2000 * $this->weight;
        // } else {
        //     return $this->taxes + ($outcomeWeight  / $rate) * $this->weight + 1000 * $this->weight;
        // }
        $outcomeWeight = (int)optional(Option::where('name', 'outcome_weight')->first())->value;

        if ($this->cubic_meters == 0) {
            return 0;
        }

        $rate = $this->weight / $this->cubic_meters;

        if (
            $this->weight < 1000
        ) {
            return ($outcomeWeight  / $rate) * $this->weight + 3000 * $this->weight;
        } elseif ($this->weight >= 1000 && $this->weight < 3000) {
            return ($outcomeWeight  / $rate) * $this->weight + 2000 * $this->weight;
        } else {
            return ($outcomeWeight  / $rate) * $this->weight + 1000 * $this->weight;
        }
    }

    public function getCommissionByCubicMetersAttribute()
    {
        // $outcomeWeight = (int)optional(Option::where('name', 'outcome_weight')->first())->value;

        // if ($this->cubic_meters < 10) {
        //     return $this->taxes + $outcomeWeight * $this->cubic_meters + 500000 * $this->cubic_meters;
        // } elseif ($this->cubic_meters >= 10 && $this->cubic_meters < 30) {
        //     return $this->taxes + $outcomeWeight * $this->cubic_meters + 300000 * $this->cubic_meters;
        // } else {
        //     return $this->taxes + $outcomeWeight * $this->cubic_meters + 200000 * $this->cubic_meters;
        // }
        $outcomeWeight = (int)optional(Option::where('name', 'outcome_weight')->first())->value;

        if ($this->cubic_meters < 10) {
            return $outcomeWeight * $this->cubic_meters + 500000 * $this->cubic_meters;
        } elseif ($this->cubic_meters >= 10 && $this->cubic_meters < 30) {
            return $outcomeWeight * $this->cubic_meters + 300000 * $this->cubic_meters;
        } else {
            return $outcomeWeight * $this->cubic_meters + 200000 * $this->cubic_meters;
        }
    }

    public function getCostAgainstOutcomeAttribute()
    {
        // if (!$this->is_calculated_cost) return 0;

        $fareByWeight = $this->fare_by_weight;
        $fareByCubicMeters = $this->fare_by_cubic_meters;

        if ($fareByWeight > $fareByCubicMeters) return $this->getCommissionByWeightAttribute() + $this->sale_taxes;

        return $this->getCommissionByCubicMetersAttribute() + $this->sale_taxes;
    }

    public function getCommissionAttribute()
    {
        return $this->sale_commission;
    }

    public function getIsDeclaredAttribute()
    {
        return $this->declarations->count() > 0;
    }

    public function getSaleTaxesAttribute()
    {
        return $this->report_taxes1 + $this->report_taxes2;
    }

    public function getSaleNetIncomeAttribute()
    {
        return $this->revenue;
        //  - $this->taxes;
    }

    public function getSaleRevenueAttribute()
    {
        if ($this->code === 'D817-13') {
            Log::info('#' . $this->code . ' getSaleRevenueAttribute');
            Log::info('#' . $this->code . ' cost_vietnam ' . $this->cost_vietnam);
            Log::info('#' . $this->code . ' revenue ' . $this->revenue);
            Log::info('#' . $this->code . ' cost_vietnam ' . $this->cost_vietnam);
            Log::info('#' . $this->code . ' cost_china1_vnd ' . $this->cost_china1_vnd);
            Log::info('#' . $this->code . ' cost_china2_vnd ' . $this->cost_china2_vnd);
        }

        if ($this->cost_vietnam < 0) {
            return $this->revenue -  $this->cost_china1_vnd - $this->cost_china2_vnd;
        }

        // return $this->revenue - $this->taxes - $this->cost_china1_vnd - $this->cost_china2_vnd;
        return $this->revenue - $this->cost_vietnam - $this->cost_china1_vnd - $this->cost_china2_vnd;
    }

    public function getSaleRevenueFromReportAttribute()
    {
        return 0.04 * ($this->revenue - $this->taxes - $this->cost_china1_vnd - $this->cost_china2_vnd - $this->other_costs);
    }

    public function getSaleCommissionAttribute()
    {
        if ($this->sale_revenue - $this->cost_against_outcome < 0) {
            return $this->sale_revenue - $this->cost_against_outcome;
        }

        return 0.05 * $this->sale_revenue;
    }

    public function getDeliveryDateFormatAttribute()
    {
        if (!is_null($this->delivery_date)) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $this->delivery_date)->format('d-m-Y');
        }

        return null;
    }

    public function getCreatedAtFormatAttribute()
    {
        if (!is_null($this->created_at)) {
            return Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d-m-Y');
        }

        return null;
    }

    public function getArrivalDateFormatAttribute()
    {
        if (!is_null($this->truck)) {
            if (!is_null($this->truck->arrival_date)) {
                return Carbon::createFromFormat('Y-m-d', $this->truck->arrival_date)->format('d-m-Y');
            }
        }

        return null;
    }

    public function getUnseenMessagesAttribute()
    {
        $count = $this->messages->count();

        $countSeen =  $this->messages->filter(function ($message) {
            return $message->messageViews->filter(function ($messageView) {
                return $messageView->user_id == auth()->id();
            })->count() > 0;
        })->count();

        return $count - $countSeen;
    }

    public function notifyAddress()
    {
        return $this->hasOne(NotifyAddress::class, 'order_id', 'id');
    }

    public function getIsNotifiedAddressAttribute()
    {
        return $this->notifyAddress()->first() !== null;
    }
}
