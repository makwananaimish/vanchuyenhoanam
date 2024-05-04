<?php

namespace App\Observers;

use App\CostChina;
use App\Option;
use App\Order;
use App\Pack;
use App\Services\CostChinaService;
use App\Services\Webhook;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OrderObserver
{
    protected CostChinaService $costChinaService;

    private $webhook;

    public function __construct(CostChinaService $costChinaService, Webhook $webhook)
    {
        $this->costChinaService = $costChinaService;
        $this->webhook = $webhook;
    }

    private function postOrderToWebhook(Order $order)
    {
        try {
            $data = $order->setAppends([
                'rmb_to_vnd',
                'cost_china1_vnd',
                'cost_china2_vnd',
                'revenue',
                'paid',
                'debt',
                'status_text',
            ])->only([
                'id',
                'code',
                'bill',
                'rmb_to_vnd',
                'cost_china1_vnd',
                'cost_china2_vnd',
                'cost_vietnam',
                'revenue',
                'paid',
                'debt',
                'status_text',
            ]);

            $this->webhook->postToWebhookDotSite($data);
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Handle the order "created" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function created(Order $order)
    {
        $this->postOrderToWebhook($order);

        $rmbToVnd = Option::where('name', 'rmb_to_vnd')->first()->value;
        $order->rmb_to_vnd = $rmbToVnd;
        $order->save();

        if ($order->isDirty(['cost_china1', 'cost_china2'])) {
            $costChina = CostChina::where('order_id', $order->id)->orderBy('id', 'DESC')->first();

            if ($costChina) {
                $balance = $this->costChinaService->getBalance();

                $addAmount = $costChina->amount + $costChina->amount2;

                CostChina::create([
                    'type' => CostChina::TYPE_TOP_UP,
                    'amount' => $addAmount,
                    'date' => now(),
                    'content' => "Hủy giao dịch #{$costChina->id}",
                    'balance' => $balance + $addAmount
                ]);
            }

            $balance = $this->costChinaService->getBalance();

            $amount = $order->cost_china1 + $order->cost_china2;

            $costChina = CostChina::create([
                'type' => CostChina::TYPE_OTHER,
                'order_id' => $order->id,
                'amount' => $order->cost_china1,
                'amount2' => $order->cost_china2,
                'date' => now(),
                'content' => "Cập nhật vận đơn {$order->code}",
                'balance' => $balance - $amount
            ]);

            Log::info("Created cost china #{$costChina->id}");
        }
    }

    /**
     * Handle the order "updated" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function updated(Order $order)
    {
        $this->postOrderToWebhook($order);

        if ($order->isDirty(['cost_china1', 'cost_china2'])) {

            $costChina = CostChina::where('order_id', $order->id)->orderBy('id', 'DESC')->first();

            if ($costChina) {
                $balance = $this->costChinaService->getBalance();

                $addAmount = $costChina->amount + $costChina->amount2;

                CostChina::create([
                    'type' => CostChina::TYPE_TOP_UP,
                    'amount' => $addAmount,
                    'date' => now(),
                    'content' => "Hủy giao dịch #{$costChina->id}",
                    'balance' => $balance + $addAmount
                ]);
            }

            $balance = $this->costChinaService->getBalance();

            $amount = $order->cost_china1 + $order->cost_china2;

            CostChina::create([
                'type' => CostChina::TYPE_OTHER,
                'order_id' => $order->id,
                'amount' => $order->cost_china1,
                'amount2' => $order->cost_china2,
                'date' => now(),
                'content' => "Cập nhật vận đơn {$order->code}",
                'balance' => $balance - $amount
            ]);
        }
    }

    /**
     * Handle the order "deleted" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function deleted(Order $order)
    {
        //
    }

    /**
     * Handle the order "restored" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function restored(Order $order)
    {
        //
    }

    /**
     * Handle the order "force deleted" event.
     *
     * @param  \App\Order  $order
     * @return void
     */
    public function forceDeleted(Order $order)
    {
        //
    }
}
