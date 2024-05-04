<?php

namespace App\Http\Controllers\App;

use App\CostChina;
use App\Services\CostChinaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class CostChinaController extends BaseController
{
    public function index(CostChinaService $costChinaService)
    {
        $fromDate = request('from_date');
        $toDate = request('to_date');

        $query = CostChina::query();

        if (!is_null($fromDate)) {
            $query = $query->whereDate('date', '>=', $fromDate);
        }

        if (!is_null($toDate)) {
            $query = $query->whereDate('date', '<=', $toDate);
        }

        $costs = $query->orderBy('id', 'DESC')
            ->paginate();

        $balance = $costChinaService->getBalance();

        return view('app.cost_china.index', [
            'costs' => $costs,
            'balance' => $balance,
        ]);
    }

    public function create(Request $request, CostChinaService $costChinaService)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => [
                'string',
                Rule::in([
                    CostChina::TYPE_OTHER,
                    CostChina::TYPE_TOP_UP
                ])
            ],
            'date' => [
                'required',
            ],
            'content' => [
                'required',
                'string',
            ],
            'amount' => [
                'required',
                'integer',
                'gt:0',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to("/cost_china#create-cost-china-{$data['type']}")
                ->withErrors($validator)
                ->withInput();
        }

        $balance = $costChinaService->getBalance();

        if ($data['type'] === CostChina::TYPE_TOP_UP) {
            $balance += $data['amount'];
        } else {
            $balance -= $data['amount'];
        }
        $data['balance'] = $balance;

        CostChina::create($data);

        return redirect()->back();
    }
}
