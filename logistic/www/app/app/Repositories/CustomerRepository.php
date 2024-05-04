<?php

namespace App\Repositories;

use App\Repositories\Traits\CustomerAuth;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerRepository
{
    use CustomerAuth;

    public function getAuthUserAndLock()
    {
        $user = $this->user();

        return DB::table('customers')
            ->lockForUpdate()
            ->where('id', $user->id)
            ->first();
    }

    public function lockAndFind($id)
    {
        return DB::table('customers')
            ->lockForUpdate()
            ->where('id', $id)
            ->first();
    }

    public function updateBalance($id, $value)
    {
        return DB::table('customers')
            ->where('id', $id)
            ->update([
                'balance' => $value
            ]);
    }

    public function canAcceptDeposit($transactionId)
    {
        $transaction = Transaction::lockForUpdate()
            ->find($transactionId);

        Log::info("transaction type: {$transaction->type}");
        Log::info("transaction status: {$transaction->status}");

        return $transaction->type === Transaction::TYPE_DEPOSIT && $transaction->status === Transaction::STATUS_TEXT_PROCESSING;
    }

    public function acceptDeposit($transactionId)
    {
        $transaction = Transaction::lockForUpdate()
            ->find($transactionId);

        $customer = $this->lockAndFind($transaction->customer_id);

        $this->updateBalance($customer->id, $customer->balance + $transaction->amount);

        $transaction->status = Transaction::STATUS_TEXT_COMPLETED;
        return $transaction->save();
    }

    public function canAcceptWithdraw($transactionId)
    {
        $transaction = Transaction::lockForUpdate()
            ->find($transactionId);

        return $transaction->type === Transaction::TYPE_WITHDRAWAL && $transaction->status === Transaction::STATUS_TEXT_PROCESSING;
    }

    public function acceptWithdraw($transactionId)
    {
        $transaction = Transaction::lockForUpdate()
            ->find($transactionId);

        $transaction->status = Transaction::STATUS_TEXT_COMPLETED;

        return $transaction->save();
    }

    public function canCancelWithdraw($transactionId)
    {
        $transaction = Transaction::lockForUpdate()
            ->find($transactionId);

        return $transaction->type === Transaction::TYPE_WITHDRAWAL && $transaction->status === Transaction::STATUS_TEXT_PROCESSING;
    }

    public function cancelWithdraw($transactionId)
    {
        $transaction = Transaction::lockForUpdate()
            ->find($transactionId);

        $customer = $this->lockAndFind($transaction->customer_id);

        $this->updateBalance($customer->id, $customer->balance + $transaction->amount);

        $transaction->status = Transaction::STATUS_TEXT_CANCELLED;

        return $transaction->save();
    }

    public function canCancelDeposit($transactionId)
    {
        $transaction = Transaction::lockForUpdate()
            ->find($transactionId);

        return $transaction->type === Transaction::TYPE_DEPOSIT && $transaction->status === Transaction::STATUS_TEXT_PROCESSING;
    }

    public function cancelDeposit($transactionId)
    {
        $transaction = Transaction::lockForUpdate()
            ->find($transactionId);
        $transaction->status = Transaction::STATUS_TEXT_CANCELLED;

        return $transaction->save();
    }
}
