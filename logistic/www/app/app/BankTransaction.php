<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class BankTransaction extends Model
{
    public const VIETCOMBANK = 'Vietcombank';
    public const TECHCOMBANK = 'Techcombank';
    public const VIETINCOMBANK = 'Vietinbank';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'bank',
        'content',
        'amount',
        'date',
    ];

    public function getAmountFormatAttribute()
    {
        return number_format($this->amount, 0, ',', '.');
    }

    public function getDateFormatAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->date)->format('d-m-Y H:i:s');
    }
}
