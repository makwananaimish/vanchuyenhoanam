<?php

namespace App\Http\Controllers\App;

use App\Option;
use App\Repositories\CustomerRepository;
use App\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramBotController extends BaseController
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function webhook(Request $request)
    {
        try {
            $data = $request->all();
            Log::info($data);

            $telegramApprover = optional(Option::where('name', 'telegram_approver')->first())->value;

            $username = $data['callback_query']['from']['username'];

            Log::info("Approver {$telegramApprover}");
            Log::info("Username {$username}");
            $compare = (int) ($username == $telegramApprover);
            Log::info("username == telegramApprover {$compare}");

            if ($username != $telegramApprover) return;

            if (isset($data['callback_query']['data'])) {
                $queryData = json_decode($data['callback_query']['data'], true);
                $transactionId = $queryData['id'];
                $callbackQueryType = $queryData['type'];
                Log::info("transactionId {$transactionId}");
                Log::info("callbackQueryType {$callbackQueryType}");

                Log::info("transaction id: $transactionId");

                $chatId = $data['callback_query']['message']['chat']['id'];
                Log::info("chatId: $chatId");

                $messageId = $data['callback_query']['message']['message_id'];
                Log::info("messageId: $messageId");

                // $transaction = Transaction::find($transactionId);
                // Log::info($transaction->toArray());

                DB::beginTransaction();

                if ($callbackQueryType == 'accept') {
                    $canAcceptDeposit = $this->customerRepository->canAcceptDeposit($transactionId);

                    Log::info("canAcceptDeposit: " . (int) $canAcceptDeposit);

                    if ($canAcceptDeposit) {
                        Log::info("canAcceptDeposit");

                        $this->customerRepository->acceptDeposit($transactionId);
                    }
                }

                if ($callbackQueryType == 'cancel') {
                    if ($this->customerRepository->canCancelDeposit($transactionId)) {
                        Log::info("canCancelDeposit");

                        $this->customerRepository->cancelDeposit($transactionId);
                    }
                }

                DB::commit();
            }
        } catch (Exception $e) {
            Log::error("Accept error: {$e->getMessage()}");

            throw $e;
        }
    }
}
