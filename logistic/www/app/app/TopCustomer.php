<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopCustomer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'month',
        'year',
        'revenue',
        'customer_id'
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }
}
