<?php

namespace App\Services;

use App\Repositories\CustomerRepository;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TCB
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getTransactionFromAndroid($rawTransaction)
    {
        $transaction = [];

        $transaction['amount'] = (int)  str_replace([' VND ', ','], '', $rawTransaction['title']);
        $transaction['content'] =   $rawTransaction['text'];
        $transaction['format_date'] = Carbon::now()->format('Y-m-d H:i:s');

        return $transaction;
    }

    public function contentIncludeCustomer($content, $customer)
    {
        $content = str_replace('CT DEN', '', $content);
        $content = preg_replace('/\s+/', '', $content);

        $description = genTransactionDescription($customer);

        // $description = $customer->id . 'ID';

        Log::info("description: {$description}");

        return strpos($content, $description) !== false;
    }

    public function checkV2($rawTransaction, $customer)
    {
        return $rawTransaction['amount'] > 0 && $this->contentIncludeCustomer($rawTransaction['content'], $customer);
    }

    public function isProcessedV2($rawTransaction, $customer)
    {
        $description = genTransactionDescription($customer);

        // $description = $customer->id . 'ID';

        $transaction = Transaction::query()
            ->lockForUpdate()
            ->where('description', $description)
            ->where('raw_date', $rawTransaction['format_date'])
            ->first();

        return !is_null($transaction);
    }

    public function processV2($rawTransactions)
    {
        return DB::table('customers')
            ->whereNull('deleted_at')
            ->orderByDesc('id')
            ->chunk(100, function ($customers) use ($rawTransactions) {
                foreach ($customers as $customer) {

                    foreach ($rawTransactions as $rawTransaction) {
                        DB::beginTransaction();

                        try {
                            $description = genTransactionDescription($customer);
                            // $description = $customer->id . 'ID';

                            $isProcessed = $this->isProcessedV2($rawTransaction, $customer);
                            $check = $this->checkV2($rawTransaction, $customer);

                            if ($check) {
                                Log::info("Checking #{$customer->code} and {$rawTransaction['content']}");
                                Log::info(" => description #{$description}");

                                Log::info(" => isProcessed : " . (int)$isProcessed);
                                Log::info(" => check : " . (int)$check);
                                Log::info(" ========================");
                            }

                            if (!$isProcessed && $check) {
                                $customer = $this->customerRepository->lockAndFind($customer->id);

                                $balance = $customer->balance + $rawTransaction['amount'];
                                $this->customerRepository->updateBalance($customer->id, $balance);

                                $transaction = Transaction::create([
                                    'type' => Transaction::TYPE_DEPOSIT,
                                    'deposit_type' => Transaction::DEPOSIT_TYPE_AUTO,
                                    'user_id' => null,
                                    'customer_id' => $customer->id,
                                    'amount' => $rawTransaction['amount'],
                                    'balance' => $balance,
                                    'description' => $description,
                                    'status' => Transaction::STATUS_TEXT_COMPLETED,
                                    'raw_date' => $rawTransaction['format_date'],
                                    'raw_content' => $rawTransaction['content'],
                                ]);

                                Log::info(" = created transaction #{$transaction->id}");
                            }

                            DB::commit();
                        } catch (\Throwable $th) {
                            Log::error(" = error : {$th->getMessage()}");
                        }
                    }
                }
            });
    }
}
