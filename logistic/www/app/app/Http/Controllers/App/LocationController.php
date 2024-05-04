<?php

namespace App\Http\Controllers\App;

use App\Location;
use App\Option;
use App\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class LocationController extends BaseController
{
    public function index(Request $request)
    {
        $query = Location::query();

        $locations =  $query
            // ->where('type', Location::IN_CHINA)
            ->where('name', '!=', 'Hoàn Thành')
            ->orderBy('type', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();

        $rmbToVND = optional(Option::where('name', 'rmb_to_vnd')->first())->value;
        $outcomeWeight = optional(Option::where('name', 'outcome_weight')->first())->value;
        $telegramBotToken = optional(Option::where('name', 'telegram_bot_token')->first())->value;
        $telegramChatId = optional(Option::where('name', 'telegram_chat_id')->first())->value;
        $telegramWebhook = optional(Option::where('name', 'telegram_webhook')->first())->value;
        $telegramApprover = optional(Option::where('name', 'telegram_approver')->first())->value;
        $apiToken = optional(Option::where('name', 'api_token')->first())->value;
        $apiWhitelistIp = optional(Option::where('name', 'api_whitelist_ip')->first())->value;
        $whitelistAccountNumbers = optional(Option::where('name', 'whitelist_account_numbers')->first())->value;
        $webhookNotiDebt = optional(Option::where('name', 'webhook_noti_debt')->first())->value;

        $shippingMethods = ShippingMethod::all();

        return view('app.location.index', [
            'locations' => $locations,
            'rmbToVND' => $rmbToVND,
            'outcomeWeight' => $outcomeWeight,
            'telegramBotToken' => $telegramBotToken,
            'telegramChatId' => $telegramChatId,
            'telegramWebhook' => $telegramWebhook,
            'telegramApprover' => $telegramApprover,
            'apiToken' => $apiToken,
            'apiWhitelistIp' => $apiWhitelistIp,

            'shippingMethods' => $shippingMethods,
            'whitelistAccountNumbers' => $whitelistAccountNumbers,
            'webhookNotiDebt' => $webhookNotiDebt,
        ]);
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'type' => [
                Rule::in([
                    Location::IN_CHINA,
                    Location::IN_VIETNAM,
                    Location::TRANSSHIPMENT
                ])
            ],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations')->whereNull('deleted_at')
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('locations#create-location')
                ->withErrors($validator)
                ->withInput();
        }

        $location = Location::create($data);

        activity()
            ->performedOn($location)
            ->causedBy(auth()->user())
            ->withProperties($data)
            ->log('Tạo kho');

        return  redirect()->route('location.index');
    }

    public function update(Request $request, Location $location)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('locations')->ignore($location->id)
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('locations#create-location')
                ->withErrors($validator)
                ->withInput();
        }

        activity()
            ->performedOn($location)
            ->causedBy(auth()->user())
            ->withProperties($data)
            ->log('Cập nhật kho');

        $location->fill($data);
        $location->save();

        return redirect()
            ->route('location.index');
    }

    public function destroy(Location $location)
    {
        if ($location->name === 'Hoàn Thành') redirect()->back();

        activity()
            ->performedOn($location)
            ->causedBy(auth()->user())
            ->log('Xoá kho');

        $location->delete();

        return redirect()->back();
    }

    public function createShippingMethod(Request $request)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('locations#create-shipping-method')
                ->withErrors($validator)
                ->withInput();
        }

        ShippingMethod::create($data);

        return  redirect()->route('location.index');
    }

    public function updateShippingMethod(Request $request, ShippingMethod $shippingMethod)
    {
        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'string',
                'max:255',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('locations#create-shipping-method')
                ->withErrors($validator)
                ->withInput();
        }

        $shippingMethod->fill($data);
        $shippingMethod->save();

        return redirect()
            ->route('location.index');
    }

    public function destroyShippingMethod(ShippingMethod $shippingMethod)
    {
        $shippingMethod->delete();

        return redirect()->back();
    }
}
