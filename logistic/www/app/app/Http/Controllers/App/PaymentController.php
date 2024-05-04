<?php

namespace App\Http\Controllers\App;

use App\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class PaymentController extends BaseController
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'order_id' => [
                'required',
                Rule::exists('orders', 'id')
            ],
            'amount' => [
                'required',
                'integer',
                'gt:0',
            ],
            'image' => [
                'required',
                'file',
            ],
        ]);

        $redirect = $data['redirect'];

        if ($validator->fails()) {
            return redirect()
                ->to("$redirect#create-payment-{$data['order_id']}")
                ->withErrors($validator)
                ->withInput();
        }

        $file = $request->file('image');

        $newFilename = Str::random() . '.' . $file->getClientOriginalExtension();

        $_file = $file->move(public_path('files'), $newFilename);

        Payment::create([
            'order_id' => $data['order_id'],
            'amount' => $data['amount'],
            'image' => $_file->getFilename(),
        ]);

        return redirect()->to($redirect);
    }

    public function destroy(Payment $payment)
    {
        $payment->delete();

        return redirect()->back();
    }
}
