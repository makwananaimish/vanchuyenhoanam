<?php

namespace App\Http\Controllers\App;

use App\Http\Controllers\Controller;
use App\Message;
use App\MessageView;
use App\Order;
use App\Repositories\Traits\CustomerAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class MessageController extends Controller
{
    use CustomerAuth;

    public function get(Order $order)
    {
        $messages = Message::where('order_id', $order->id)
            // ->orderBy('id', 'DESC')
            ->get();

        if (auth()->check()) {
            $userId = auth()->id();
        } else {
            $userId = $this->user()->id;
        }

        foreach ($messages as $message) {
            MessageView::firstOrCreate([
                'user_id' => $userId,
                'message_id' => $message->id
            ], [
                'user_id' => $userId,
                'message_id' => $message->id
            ]);
        }

        return response()->json($messages);
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'order_id' => [
                'required',
                'string',
                'max:255',
                Rule::exists('orders', 'id')->whereNull('deleted_at')
            ],
            'content' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ]);
        }

        $data = [
            'order_id' => $data['order_id'],
            'content' => $data['content'],
        ];

        if (auth()->check()) {
            $data['user_id'] = auth()->id();
        } elseif (auth()->guard('customer')->check()) {
            $data['customer_id'] = auth()->guard('customer')->id();
        }

        $message = Message::create($data);

        return response()->json($message);
    }
}
