<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable, SoftDeletes;

    const ROLE_ADMIN = 'ADMIN';
    const ROLE_SELLER = 'SELLER';
    const ROLE_VN_INVENTORY = 'VN_INVENTORY';
    const ROLE_CN_INVENTORY = 'CN_INVENTORY';
    const ROLE_ACCOUNTANT = 'ACCOUNTANT';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'location_id',
        'permissions',
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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'permissions' => 'array',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'is_admin',
        'is_manager',
        'is_seller',
        'avatar_url',
    ];

    public function getIsAdminAttribute()
    {
        return $this->role === self::ROLE_ADMIN || $this->email === 'admin@example.com' || $this->email === 'ioreilly@example.com';
    }

    public function getIsManagerAttribute()
    {
        return $this->email === 'manager@example.com';
    }

    public function getIsSellerAttribute()
    {
        return $this->role === self::ROLE_SELLER || $this->email === 'wkrajcik@example.net';
    }

    public function getIsAccountantAttribute()
    {
        return $this->role === self::ROLE_ACCOUNTANT;
    }

    public function getIsVnInventoryAttribute()
    {
        return $this->role === self::ROLE_VN_INVENTORY;
    }

    public function getIsCnInventoryAttribute()
    {
        return $this->role === self::ROLE_CN_INVENTORY;
    }

    public function getAvatarUrlAttribute()
    {
        $avatar = $this->avatar()->first();

        if ($avatar) {
            return $avatar->image;
        }

        return '/assets/img/user.png';
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function avatar()
    {
        return $this->hasOne(Avatar::class, 'user_id');
    }
}
