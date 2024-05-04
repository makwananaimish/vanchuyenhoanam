<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderDeclaration extends Model
{
    use SoftDeletes;

    public const TYPE_NORMAL = 'normal';
    public const TYPE_MACHINE = 'machine';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'order_id',
        'code',
        'images',
        'name',

        'length',
        'width',
        'height',
        'size',

        'brand',
        'material',

        'weight_per_product',
        'quantity_per_pack',
        'pack_quantity',
        'quantity',

        'voltage_power_parameters',

        'weight_per_box',

        'box_length',
        'box_width',
        'box_height',
        'box_size',

        'cubic_meters',
        'weight',
        'hs_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'images' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'cubic_meters_format',
        'weight_format',
    ];

    public function getCubicMetersFormatAttribute()
    {
        return number_format($this->cubic_meters, 0, ',', '.');
    }

    public function getWeightFormatAttribute()
    {
        return number_format($this->weight, 0, ',', '.');
    }
}
