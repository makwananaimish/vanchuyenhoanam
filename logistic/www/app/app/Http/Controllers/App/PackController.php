<?php

namespace App\Http\Controllers\App;

use App\Pack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class PackController extends BaseController
{
    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'order_id' => [
                'required',
                Rule::exists('orders', 'id')
            ],
            'quantity' => [
                'required',
                'integer',
                'gt:0',
            ],

            'height' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'width' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'depth' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'weight' => [
                'required',
                'numeric',
                'gte:0',
            ],
        ]);

        $redirect = $data['redirect'];

        if ($validator->fails()) {
            return redirect()
                ->to("$redirect#create-pack-{$data['order_id']}")
                ->withErrors($validator)
                ->withInput();
        }

        Pack::create([
            'order_id' => $data['order_id'],
            'quantity' => $data['quantity'],
            'height' => $data['height'],
            'width' => $data['width'],
            'depth' => $data['depth'],
            'weight' => $data['weight'],
            'status' => Pack::IN_PROGRESS,
        ]);

        return redirect()->to($redirect);
    }

    public function update(Pack $pack, Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'order_id' => [
                'required',
                Rule::exists('orders', 'id')
            ],
            'quantity' => [
                'required',
                'integer',
                'gt:0',
            ],
            'height' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'width' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'depth' => [
                'required',
                'numeric',
                'gte:0',
            ],
            'weight' => [
                'required',
                'numeric',
                'gte:0',
            ],
        ]);

        $redirect = $data['redirect'];

        if ($validator->fails()) {
            return redirect()
                ->to("$redirect#update-pack-{$pack->id}")
                ->withErrors($validator)
                ->withInput();
        }

        $pack->fill($data);
        $pack->save();

        return redirect()->to($redirect);
    }

    public function destroy(Pack $pack)
    {
        $pack->delete();

        return redirect()->back();
    }

    public function updateStatus(Pack $pack, Request $request)
    {
        $data = $request->all();

        if ($pack->can_delivery && auth()->check()) {
            $pack->status = $data['status'];
            $pack->save();
        }

        return response()
            ->json($pack);
    }
}
