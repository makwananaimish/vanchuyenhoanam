<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Activitylog\Traits\LogsActivity;

class Customer extends Authenticatable
{
    use SoftDeletes, LogsActivity;

    const NONAME_CODE = 'KHONGTEN';
    const EXPRESS_CODE = 'CHUYENPHATNHANH';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'code',
        'phone',
        'address',
        'password',
        'balance',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        // 'revenue',
        // 'debt',
        // 'paid',
        // 'total_deposit',
        // 'total_spend',


        'balance_formatted'
    ];

    protected static $logAttributes = [
        'name',
        'code',
        'phone',
    ];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function trucks()
    {
        return $this->belongsToMany(Truck::class, 'orders');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRevenueAttribute()
    {
        return $this->orders->sum('revenue');
    }

    public function getDebtAttribute()
    {
        $debt = $this->orders->sum('debt');

        if ($debt >= -1000 && $debt < 0) {
            return 0;
        }

        return $debt;
    }

    public function getPaidAttribute()
    {
        return $this->revenue - $this->debt;
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function getTotalDepositAttribute()
    {
        return $this->transactions()->where('type', Transaction::TYPE_DEPOSIT)->where('status', Transaction::STATUS_TEXT_COMPLETED)->sum('amount');
    }

    public function getTotalSpendAttribute()
    {
        return $this->transactions()->where('type', Transaction::TYPE_PAYMENT)->where('status', Transaction::STATUS_TEXT_COMPLETED)->sum('amount');
    }

    public function avatar()
    {
        return $this->hasOne(Avatar::class, 'user_id');
    }

    public function getAvatarUrlAttribute()
    {
        $avatar = $this->avatar()->first();

        if ($avatar) {
            return $avatar->image;
        }

        return '/assets/img/user.png';
    }

    public function getBalanceFormattedAttribute()
    {
        return number_format($this->balance, 0, '.', '.');
    }

    public function getCreatedAtFormattedAttribute()
    {
        return Carbon::createFromFormat('Y-m-d H:i:s', $this->created_at)->format('d-m-Y');
    }
}
