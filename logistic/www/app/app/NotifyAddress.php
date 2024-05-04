<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotifyAddress extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'date',
        'address',
        'phone',
        'name',
        'note',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
