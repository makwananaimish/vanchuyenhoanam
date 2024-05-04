<?php

namespace App\Services;

use App\CostChina;

class CostChinaService
{
    public function getBalance()
    {
        $topUp = CostChina::where('type', CostChina::TYPE_TOP_UP)->sum('amount');
        $other = CostChina::where('type', CostChina::TYPE_OTHER)->sum('amount') + CostChina::where('type', CostChina::TYPE_OTHER)->sum('amount2');

        return $topUp - $other;
    }
}
