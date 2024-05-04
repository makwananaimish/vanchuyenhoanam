<?php

namespace App\Http\Controllers\App;

use App\User;
use Illuminate\Http\Request;
use Spatie\Activitylog\Models\Activity;

class LogController extends BaseController
{
    public function index(Request $request)
    {
        $causerId = request('causer_id');
        $orderCode = request('order_code');

        $query = Activity::query();

        if ($causerId) {
            $query = $query->where('causer_id', $causerId);
        }

        if ($orderCode) {
            $query =
                $query
                ->where('subject_type', 'App\Order')
                ->where('properties', 'LIKE', "%{$orderCode}%");
        }

        $activities =  $query
            ->orderBy('id', 'DESC')
            ->paginate();

        $users = User::all();

        return view('app.log.index', [
            'activities' => $activities,
            'users' => $users
        ]);
    }
}
