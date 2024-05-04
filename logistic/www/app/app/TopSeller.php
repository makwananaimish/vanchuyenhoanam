<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TopSeller extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'month',
        'year',
        'commission',
        'seller_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }
}
