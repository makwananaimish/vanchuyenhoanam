<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pack extends Model
{
    use SoftDeletes;

    public const IN_PROGRESS = 0;

    public const DELIVERED = 1;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'quantity',
        'height',
        'width',
        'depth',
        'weight',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'cubic_meters',
        // 'weight',
        // 'can_delivery',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getCubicMetersAttribute()
    {
        return  $this->height * $this->width * $this->depth / 1000000;
    }

    public function getWeightAttribute($value)
    {
        return $value;
    }

    public function getCanDeliveryAttribute()
    {
        return optional($this->order)->can_delivery;
    }
}
