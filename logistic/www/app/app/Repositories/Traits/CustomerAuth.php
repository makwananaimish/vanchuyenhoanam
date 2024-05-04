<?php

namespace App\Repositories\Traits;

use Illuminate\Support\Facades\DB;

trait CustomerAuth
{
    public static function check()
    {
        return auth()->guard('customer')->check();
    }

    public static function user()
    {
        if (!auth()->guard('customer')->check()) {
            return null;
        }

        return DB::table('customers')
            ->where('id', auth()->guard('customer')->id())
            ->first();
    }
}
