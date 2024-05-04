<?php

namespace App\Http\Controllers\App;

use App\OrderDeclaration;
use App\Services\Uploader;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrderDeclarationController extends BaseController
{
    public function create(Request $request, Uploader $uploader)
    {
        $data = $request->all();

        $type = Arr::get($data, 'type');

        $rules = [
            'type' => [
                'required',
                Rule::in([
                    OrderDeclaration::TYPE_NORMAL,
                    OrderDeclaration::TYPE_MACHINE
                ])
            ],
            'order_id' => [
                'required',
                Rule::exists('orders', 'id')
            ],
            'code' => [
                'nullable',
                // 'required',
                'string',
                'max:255',
            ],
            'images' => [
                'nullable',
                'max:3'
            ],
            'images.*' => [
                'image',
                'max:2048',
            ],
            'name' => [
                'required',
                'string',
                'max:255',
            ],
            'brand' => [
                'required',
                'string',
                'max:255',
            ],
            'material' => [
                'required',
                'string',
                'max:255',
            ],

            'weight_per_product' => [
                'nullable',
                // 'required',
                // 'numeric',
                // 'gte:0',
            ],
            'quantity_per_pack' => [
                'nullable',
                // 'required',
                'integer',
                'gte:0',
            ],
            'pack_quantity' => [
                'nullable',
                // 'required',
                'integer',
                'gte:0',
            ],
            'quantity' => [
                'nullable',
                // 'required',
                'integer',
                'gte:0',
            ],

            'voltage_power_parameters' => [
                'nullable',
                'string',
                'max:255',
            ],

            'weight_per_box' => [
                'nullable',
                // 'required',
                // 'numeric',
                // 'gte:0',
            ],

            'box_length' => [
                'nullable',
                // 'required',
                'numeric',
                'gte:0',
            ],
            'box_width' => [
                'nullable',
                // 'required',
                'numeric',
                'gte:0',
            ],
            'box_height' => [
                'nullable',
                // 'required',
                'numeric',
                'gte:0',
            ],

            'cubic_meters' => [
                'nullable',
                // 'required',
                'numeric',
                'gte:0',
            ],
            'weight' => [
                'nullable',
                // 'required',
                // 'numeric',
                // 'gte:0',
            ],
        ];

        if ($type === OrderDeclaration::TYPE_NORMAL) {
            $rules['size'] = [
                'required',
                'string',
                'max:255',
            ];
        } else if ($type === OrderDeclaration::TYPE_MACHINE) {
            $rules['voltage_power_parameters'] = [
                'required',
                'string',
                'max:255',
            ];
        }

        $validator = Validator::make($data, $rules);

        if ($validator->fails()) {
            return redirect()
                ->to(back()->getTargetUrl() . '#create-order-declaration')
                ->withErrors($validator)
                ->withInput();
        }

        $images = $request->file('images', []);
        $data['images'] = $uploader->upload($images);

        OrderDeclaration::create($data);

        return redirect()->back();
    }

    public function destroy(OrderDeclaration $declaration)
    {
        $declaration->delete();

        return redirect()->back();
    }
}
