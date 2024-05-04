<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class CostChina extends Model
{
    use SoftDeletes;

    public const TYPE_OTHER = 'other';
    public const TYPE_TOP_UP = 'top-up';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'type',
        'date',
        'content',
        'amount',
        'amount2',
        'balance',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'amount_format',
        'amount2_format',
        'balance_format',
        'date_format',
    ];

    public function getAmountFormatAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }

    public function getAmount2FormatAttribute()
    {
        return number_format($this->amount2, 0, ',', '.');
    }

    public function getBalanceFormatAttribute()
    {
        return number_format($this->balance, 0, ',', '.');
    }

    public function getDateFormatAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->date)->format('d-m-Y');
    }
}
