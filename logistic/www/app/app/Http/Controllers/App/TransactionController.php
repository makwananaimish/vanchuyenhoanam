<?php

namespace App\Http\Controllers\App;

use App\BankTransaction;
use App\Customer;
use App\Repositories\CustomerRepository;
use App\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class TransactionController extends BaseController
{
    protected CustomerRepository $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        parent::__construct();

        $this->customerRepository = $customerRepository;
    }

    public function index()
    {
        $type = request('type');
        $status = request('status');
        $depositType = request('deposit_type');
        $customerId = request('customer_id');
        $customers = Customer::all();

        $balance = 0;
        if (CustomerRepository::check()) {
            $balance = CustomerRepository::user()->balance;
        }

        $transactions = Transaction::query();

        if (!is_null($customerId)) {
            $transactions = $transactions->where('customer_id', $customerId);
        }

        if (!is_null($type)) {
            $transactions = $transactions->where('type', $type);
        }

        if (!is_null($depositType)) {
            $transactions = $transactions->where('deposit_type', $depositType);
        }

        if (!is_null($status)) {
            $transactions = $transactions->where('status', $status);
        }

        if (isSeller()) {
            $transactions = $transactions->whereHas('customer', function ($query) {
                $query->where('user_id', auth()->id());
            });
        }

        if (auth()->guard('customer')->check()) {
            $transactions = $transactions->where('customer_id', auth()->guard('customer')->id());
        }

        $transactions = $transactions

            ->orderBy('status', 'ASC')
            ->orderBy('id', 'DESC')
            ->paginate();

        return view('app.transaction.index', [
            'customers' => $customers,
            'transactions' => $transactions,
            'balance' => $balance
        ]);
    }

    public function autobank()
    {
        $bank = request('bank');
        $month = request('month');
        $accountNumber = request('account_number');

        $transactions = BankTransaction::query();

        if (!is_null($bank)) {
            $transactions = $transactions->where('bank', $bank);
        }

        if ($month) {
            $_month = Carbon::createFromFormat('Y-m', $month);

            $transactions = $transactions
                ->whereMonth('date',  $_month->month)
                ->whereYear('date', $_month->year);
        }

        if ($accountNumber) {
            $transactions = $transactions
                ->where('content', 'LIKE', "%{$accountNumber}%");
        }

        $transactions = $transactions
            ->where('content', 'NOT LIKE', '%fake transaction%')
            ->orderBy('date', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate();

        return view('app.transaction.autobank', [
            'transactions' => $transactions,
        ]);
    }

    public function showCreateForm(Request $request)
    {
        $options = $request->all();

        if (auth()->guard('customer')->check()) {
            return redirect()->route('transaction.customer.create');
        }

        $customers = filterCustomers($options);

        $allCustomers = Customer::with(['transactions'])->orderBy('id', 'DESC')->get();
        $totalDebt = getTotalDebt();

        return view('app.transaction.create', [
            'customers' => $customers,
            'allCustomers' => $allCustomers,
            'totalDebt' => $totalDebt,
        ]);
    }

    public function showCreateByCustomerForm()
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('transaction.create');
        }

        $user = $this->customerRepository->getAuthUserAndLock();

        $customer = Customer::find($user->id);

        $description = genTransactionDescription($customer);

        $qrLink = genQRQuickLink(0, $description);

        return view('app.transaction.customer.create', [
            'user' => $user,
            'qrLink' => $qrLink,
            'description' => $description,
        ]);
    }

    public function create(Request $request)
    {
        if (auth()->guard('customer')->check()) {
            return redirect()->route('transaction.customer.create');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'customer_id' => [
                'required',
                Rule::exists('customers', 'id')
            ],
            'amount' => [
                'required',
                'integer',
                'gte:100000',
                'lte:5000000000',
            ],
            'image' => [
                'image',
                'max:2048',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        // Save image
        $file = $request->file('image');

        $newFilename = Str::random() . '.' . $file->getClientOriginalExtension();

        $_file = $file->move(public_path('files'), $newFilename);

        $image = $_file->getFilename();

        // Create transaction
        $transaction = Transaction::create([
            'user_id' => auth()->id(),
            'customer_id' => $data['customer_id'],
            'type' => Transaction::TYPE_DEPOSIT,
            'deposit_type' => Transaction::DEPOSIT_TYPE_MANUAL,
            'amount' => $data['amount'],
            'image' => $image,
            'status' => Transaction::STATUS_TEXT_PROCESSING
        ]);

        $transaction = Transaction::lockForUpdate()->find($transaction->id);

        $transaction->description = $transaction->customer->code;
        $transaction->save();

        DB::commit();

        Log::info('commit');

        return redirect()->route('transaction.show', [
            'transaction' => $transaction
        ]);
    }

    public function createByCustomer(Request $request)
    {
        if (!auth()->guard('customer')->check()) {
            return redirect()->route('transaction.create');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => [
                'required',
                Rule::in([
                    Transaction::TYPE_DEPOSIT,
                    Transaction::TYPE_WITHDRAWAL
                ])
            ],
            'amount' => [
                'required',
                'integer',
                'gte:100000',
                'lte:5000000000',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();

        if ($data['type'] === Transaction::TYPE_WITHDRAWAL) {
            $user = $this->customerRepository->getAuthUserAndLock();

            if ($user->balance < $data['amount']) {
                return redirect()
                    ->back()
                    ->with('message', 'Không đủ số dư')
                    ->with('alert-class', 'alert-danger');
            } else {
                $newBalance = $user->balance - $data['amount'];
                $this->customerRepository->updateBalance($user->id, $newBalance);
            }
        }

        $transaction = Transaction::create([
            'user_id' => null,
            'customer_id' => auth()->guard('customer')->id(),
            'type' => $data['type'],
            'deposit_type' => $data['type'] === Transaction::TYPE_DEPOSIT ? Transaction::DEPOSIT_TYPE_AUTO : null,
            'amount' => $data['amount'],
            'status' => Transaction::STATUS_TEXT_PROCESSING
        ]);

        $transaction = Transaction::lockForUpdate()->find($transaction->id);

        $customer = Customer::find(auth()->guard('customer')->id());
        $transaction->description = genTransactionDescription($customer);
        $transaction->save();

        DB::commit();

        return redirect()
            ->route('transaction.show', ['transaction' => $transaction]);
    }

    public function show(Transaction $transaction)
    {
        Gate::authorize('has-transaction', $transaction);

        $transaction->with(['customer']);

        return view('app.transaction.show', ['transaction' => $transaction]);
    }

    public function cancel(Transaction $transaction)
    {
        if (auth()->check() || $transaction->customer_id === optional(CustomerRepository::user())->id) {
            DB::beginTransaction();

            if ($transaction->type === Transaction::TYPE_DEPOSIT) {
                if ($this->customerRepository->canCancelDeposit($transaction->id)) {
                    $this->customerRepository->cancelDeposit($transaction->id);

                    DB::commit();

                    return redirect()
                        ->route('transaction.index')
                        ->with('message', 'Hủy thành công')
                        ->with('alert-class', 'alert-success');
                }
            } elseif ($transaction->type === Transaction::TYPE_WITHDRAWAL) {
                if (auth()->check()) {
                    if ($this->customerRepository->canCancelWithdraw($transaction->id)) {
                        $this->customerRepository->cancelWithdraw($transaction->id);

                        DB::commit();

                        return redirect()
                            ->route('transaction.index')
                            ->with('message', 'Hủy thành công')
                            ->with('alert-class', 'alert-success');
                    }
                }
            }
        }

        return redirect()
            ->route('transaction.index')
            ->with('message', 'Có lỗi xảy ra. Vui lòng thử lại.')
            ->with('alert-class', 'alert-danger');
    }

    public function accept(Transaction $transaction, Request $request)
    {
        Gate::authorize('only-admin');

        DB::beginTransaction();

        if ($this->customerRepository->canAcceptWithdraw($transaction->id)) {
            $data = $request->all();

            $validator = Validator::make($data, [
                'image' => [
                    'image',
                    'max:2048',
                ],
            ]);

            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withErrors($validator)
                    ->withInput();
            }

            $file = $request->file('image');

            $newFilename = Str::random() . '.' . $file->getClientOriginalExtension();

            $_file = $file->move(public_path('files'), $newFilename);

            $image = $_file->getFilename();

            $this->customerRepository->acceptWithdraw($transaction->id);

            $transaction->image = $image;
            $transaction->save();

            DB::commit();

            return redirect()
                ->back()
                ->with('message', 'Duyệt thành công.')
                ->with('alert-class', 'alert-success');
        }

        return redirect()
            ->back()
            ->with('message', 'Có lỗi xảy ra. Vui lòng thử lại.')
            ->with('alert-class', 'alert-danger');
    }
}
