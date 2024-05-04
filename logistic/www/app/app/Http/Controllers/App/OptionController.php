<?php

namespace App\Http\Controllers\App;

use App\Option;
use App\Services\Webhook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Longman\TelegramBot\Telegram;

class OptionController extends BaseController
{
    public function update(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'rmb_to_vnd' => [
                'required',
                'numeric',
                'gt:0',
            ],
            'outcome_weight' => [
                'required',
                'integer',
                'gt:0',
            ],
            'telegram_bot_token' => [
                'nullable',
                'string',
            ],
            'telegram_chat_id' => [
                'nullable',
                'string',
            ],
            'telegram_webhook' => [
                'nullable',
                'string',
            ],
            'telegram_approver' => [
                'required',
                'string',
            ],
            'whitelist_account_numbers' => [
                'required',
                'string',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('location.index')
                ->withErrors($validator)
                ->withInput();
        }

        Option::where('name', 'rmb_to_vnd')
            ->update([
                'value' => $data['rmb_to_vnd'],
            ]);

        Cache::forget('rmb_to_vnd');
        Cache::forever('rmb_to_vnd', $data['rmb_to_vnd']);

        Option::where('name', 'outcome_weight')
            ->update([
                'value' => $data['outcome_weight'],
            ]);

        Option::updateOrCreate([
            'name' => 'telegram_bot_token',
        ], [
            'name' => 'telegram_bot_token',
            'value' => $data['telegram_bot_token'],
        ]);

        Option::updateOrCreate([
            'name' => 'telegram_chat_id',
        ], [
            'name' => 'telegram_chat_id',
            'value' => $data['telegram_chat_id'],
        ]);

        Option::updateOrCreate([
            'name' => 'telegram_webhook',
        ], [
            'name' => 'telegram_webhook',
            'value' => $data['telegram_webhook'],
        ]);

        Option::updateOrCreate([
            'name' => 'telegram_approver',
        ], [
            'name' => 'telegram_approver',
            'value' => $data['telegram_approver'],
        ]);

        Option::updateOrCreate([
            'name' => 'api_token',
        ], [
            'name' => 'api_token',
            'value' => $data['api_token'],
        ]);

        Option::updateOrCreate([
            'name' => 'api_whitelist_ip',
        ], [
            'name' => 'api_whitelist_ip',
            'value' => $data['api_whitelist_ip'],
        ]);

        Option::updateOrCreate([
            'name' => 'whitelist_account_numbers',
        ], [
            'name' => 'whitelist_account_numbers',
            'value' => $data['whitelist_account_numbers'],
        ]);

        Option::updateOrCreate([
            'name' => 'webhook_noti_debt',
        ], [
            'name' => 'webhook_noti_debt',
            'value' => $data['webhook_noti_debt'] ?? ' ',
        ]);

        try {
            $telegramBotToken = $data['telegram_bot_token'];

            $telegram = new Telegram($telegramBotToken);

            $result = $telegram->setWebhook($data['telegram_webhook']);

            Log::info('Set telegram webhook');
            Log::info((array) $result);
        } catch (\Throwable $th) {
        }

        return redirect()
            ->route('location.index');
    }

    public function notiDebt(Webhook $webhook)
    {
        try {
            $webhookNotiDebt = optional(Option::where('name', 'webhook_noti_debt')->first())->value;

            $webhook->post($webhookNotiDebt, [
                'message' => '[Nhắc nợ đi]'
            ]);
        } catch (\Throwable $th) {
            // throw $th;
        }

        return redirect()
            ->route('location.index');
    }
}
