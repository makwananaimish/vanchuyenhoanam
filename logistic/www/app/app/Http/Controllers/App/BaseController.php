<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;

class BaseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth:web,customer', 'is_customer:web,customer']);
    }
}
