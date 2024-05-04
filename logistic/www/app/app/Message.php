<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'user_id',
        'customer_id',
        'content',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'sender',
        'item',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function messageViews()
    {
        return $this->hasMany(MessageView::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getSenderAttribute()
    {
        if (!is_null($this->user)) {
            $username = explode('@', $this->user->email)[0];
            return "[{$username}]";
        } elseif (!is_null($this->customer)) {
            return "[{$this->customer->code}]";
        } else {
            return '[null]';
        }
    }

    public function getItemAttribute()
    {
        return "{$this->sender}: {$this->content}";
    }
}
