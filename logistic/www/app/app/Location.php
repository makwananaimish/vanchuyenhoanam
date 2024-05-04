<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Location extends Model
{
    use SoftDeletes;

    public const IN_CHINA = 1;
    public const IN_VIETNAM = 2;
    public const TRANSSHIPMENT = 3;
    public const VIETNAM_INVENTORY = 'Kho Việt Nam';
    public const VIETNAM_INVENTORY_2 = 'Việt Nam';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',
        'name',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'type_text',
    ];

    public function trucks()
    {
        return $this->hasMany(Truck::class, 'current_location_id');
    }

    public function getTypeTextAttribute()
    {
        if ($this->type == self::IN_CHINA) {
            return 'Trung Quốc';
        } elseif ($this->type == self::IN_VIETNAM || is_null($this->type)) {
            return 'Việt Nam';
        } elseif ($this->type == self::TRANSSHIPMENT) {
            return 'Điểm trung chuyển';
        } else {
            return null;
        }
    }

    public function getInVNAttribute()
    {
        return $this->type == self::IN_VIETNAM || is_null($this->type);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
