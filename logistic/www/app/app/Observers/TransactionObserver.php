<?php

namespace App\Observers;

use App\Option;
use App\Repositories\CustomerRepository;
use App\Transaction;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TransactionObserver
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    /**
     * Handle the transaction "created" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function created(Transaction $transaction)
    {
        if ($transaction->type === Transaction::TYPE_DEPOSIT) {
            try {
                $telegramBotToken = optional(Option::where('name', 'telegram_bot_token')->first())->value;
                $telegramChatId = optional(Option::where('name', 'telegram_chat_id')->first())->value;

                $telegram = new Api($telegramBotToken);

                if ($transaction->status === Transaction::STATUS_TEXT_PROCESSING) {
                    $keyboard = json_encode([
                        "inline_keyboard" => [
                            [
                                [
                                    "text" => "Xác nhận",
                                    "callback_data" => json_encode([
                                        'id' => $transaction->id,
                                        'type' => 'accept'
                                    ])
                                ],
                                [
                                    "text" => "Hủy",
                                    "callback_data" => json_encode([
                                        'id' => $transaction->id,
                                        'type' => 'cancel'
                                    ])
                                ]
                            ]
                        ]
                    ]);

                    $text = "Invoice : `{$transaction->id}`*" . PHP_EOL .
                        "Mã khách hàng : `{$transaction->customer->code}`*" . PHP_EOL .
                        "Tên khách hàng: `{$transaction->customer->name}`*" . PHP_EOL .
                        'Số tiền nạp: `' . number_format($transaction->amount, 0, '', '.') . '`*' . PHP_EOL .
                        "Hình ảnh: [Link](" . asset('files/' . $transaction->image) . ")";

                    $text = $transaction->telegram_message_text;
                    Log::info($text);

                    $message = $telegram->sendMessage([
                        'chat_id' => $telegramChatId,
                        'text' => $text,
                        'parse_mode' => 'MarkdownV2',
                        'reply_markup' => $keyboard
                    ]);

                    $transaction->telegram_message_id = $message->getMessageId();
                    $transaction->save();
                }
            } catch (\Throwable $th) {
                Log::error("Send message error: {$th->getMessage()}");

                throw $th;
            }
        }
    }

    /**
     * Handle the transaction "updated" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function updated(Transaction $transaction)
    {
        if ($transaction->isDirty('status') && is_null($transaction->balance)) {
            if ($transaction->status === Transaction::STATUS_TEXT_COMPLETED) {
                $customer = $this->customerRepository->lockAndFind($transaction->customer_id);

                $transaction->balance = $customer->balance;
                $transaction->save();
            }
        }

        if ($transaction->type === Transaction::TYPE_DEPOSIT) {
            try {
                $telegramBotToken = optional(Option::where('name', 'telegram_bot_token')->first())->value;
                $telegramChatId = optional(Option::where('name', 'telegram_chat_id')->first())->value;

                $telegram = new Api($telegramBotToken);

                if ($transaction->status === Transaction::STATUS_TEXT_CANCELLED || $transaction->status === Transaction::STATUS_TEXT_COMPLETED) {
                    try {
                        // Delete message 
                        $messageData = http_build_query([
                            'chat_id' => $telegramChatId,
                            'message_id' => $transaction->telegram_message_id,
                        ]);
                        $url = "https://api.telegram.org/bot" . $telegramBotToken . "/deleteMessage?" . $messageData;
                        file_get_contents($url);
                    } catch (\Throwable $th) {
                        Log::error("Delete message error: {$th->getMessage()}");
                    }


                    // 
                    $message = $telegram->sendMessage([
                        'chat_id' => $telegramChatId,
                        'text' => $transaction->telegram_message_text,
                        'parse_mode' => 'MarkdownV2',
                    ]);
                }
            } catch (\Throwable $th) {
                Log::error("Send message error: {$th->getMessage()}");

                throw $th;
            }
        }
    }

    /**
     * Handle the transaction "deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function deleted(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "restored" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function restored(Transaction $transaction)
    {
        //
    }

    /**
     * Handle the transaction "force deleted" event.
     *
     * @param  \App\Transaction  $transaction
     * @return void
     */
    public function forceDeleted(Transaction $transaction)
    {
        //
    }
}
