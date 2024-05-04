<?php

namespace App\Services;

use App\Customer;
use App\Repositories\CustomerRepository;
use App\Transaction;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VTB
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getTransactions($rawTransactions)
    {
        $transactions = [];

        foreach ($rawTransactions as $rawTransaction) {
            $rawTransaction['amount'] = (int) preg_replace('(^\+|,| VND)', '', $rawTransaction['amount']);

            $date = Carbon::createFromFormat('d/m/Y H:i:s', $rawTransaction['date'], 'Asia/Ho_Chi_Minh');
            $now = Carbon::createFromFormat('Y-m-d H:i:s', '2023-02-09 00:00:00', 'Asia/Ho_Chi_Minh');
            // Log::info($date->toString());
            // Log::info($now->toString());

            $rawTransaction['format_date'] = $date->format('Y-m-d H:i:s');

            if ($date->gte($now) && !empty($rawTransaction['content']) && !is_null($rawTransaction['content'])) {
                $transactions[] = $rawTransaction;
            }
        }

        return $transactions;
    }

    public function contentIncludeCustomer($content, $customer)
    {
        $content = str_replace('CT DEN', '', $content);

        $description = genTransactionDescription($customer);

        return strpos($content, $description) !== false;
    }

    public function checkV2($rawTransaction, $customer)
    {
        return $this->contentIncludeCustomer($rawTransaction['content'], $customer);
    }

    public function isProcessedV2($rawTransaction, $customer)
    {
        $description = genTransactionDescription($customer);

        $transaction = Transaction::query()
            ->lockForUpdate()
            ->where('description', $description)
            ->where('raw_date', $rawTransaction['date'])
            ->first();

        return !is_null($transaction);
    }

    public function processV2($rawTransactions)
    {
        return DB::table('customers')
            ->whereNull('deleted_at')
            // ->where('code', 'WB')
            ->orderByDesc('id')
            ->chunk(100, function ($customers) use ($rawTransactions) {
                foreach ($customers as $customer) {

                    foreach ($rawTransactions as $rawTransaction) {
                        DB::beginTransaction();

                        try {
                            // $customer = Customer::find($customer->id);
                            $description = genTransactionDescription($customer);
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
                                    'raw_date' => $rawTransaction['date'],
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
