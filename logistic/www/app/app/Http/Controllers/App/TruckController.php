<?php

namespace App\Http\Controllers\App;

use App\Customer;
use App\Location;
use App\Order;
use App\ShippingMethod;
use App\Truck;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TruckController extends BaseController
{
    public function index(Request $request)
    {
        $incompletedTruckIds = json_decode(Cache::get('incompletedTruckIds', json_encode([])));

        $locations = Location::all();
        $shippingMethods = ShippingMethod::all();

        $inChinaLocations = Location::with([
            'trucks'
        ])
            ->where('type', Location::IN_CHINA)
            ->get();

        $transshipmentPoints = Location::with([
            'trucks'
        ])
            ->where('type', Location::TRANSSHIPMENT)
            ->get();

        $withoutTransshipmentPoints = Location::with([
            'trucks'
        ])
            ->where('type', '!=', Location::TRANSSHIPMENT)
            ->get();

        $inVietnamLocations = Location::with([
            'trucks'
        ])
            ->where('type', Location::IN_VIETNAM)
            ->get();

        $query = Truck::with([
            'departureLocation',
            'currentLocation',
            // 'customers.orders.packs',
            // 'customers.orders.payments',

            'orders' => function ($q) {
                $q->with([
                    'packs',
                    'payments',
                ]);
            },
        ])
            ->whereIn('id', $incompletedTruckIds);

        $name = $request->get('name');
        $departureLocationId = $request->get('departure_location_id');
        $currentLocationId = $request->get('current_location_id');
        $departureDateFrom = $request->get('departure_date_from');
        $departureDateTo = $request->get('departure_date_to');
        $arrivalDateFrom = $request->get('arrival_date_from');
        $arrivalDateTo = $request->get('arrival_date_to');
        $shippingMethodId = $request->get('shipping_method_id');
        $page = $request->get('page', 1);

        if ($name) {
            $query = $query->where('name', 'LIKE', "%$name%");
        }

        if ($departureLocationId) {
            $query = $query->whereHas('departureLocation', function ($q) use ($departureLocationId) {
                $q->where('id', $departureLocationId);
            });
        }

        if ($currentLocationId) {
            $query = $query->where('current_location_id', $currentLocationId);
        }

        if ($departureDateFrom) {
            $query = $query->whereDate('departure_date', '>=', Carbon::createFromFormat('m/d/Y', $departureDateFrom));
        }

        if ($departureDateTo) {
            $query = $query->whereDate('departure_date', '<=', Carbon::createFromFormat('m/d/Y', $departureDateTo));
        }

        if ($arrivalDateFrom) {
            $query = $query->whereDate('arrival_date', '>=', Carbon::createFromFormat('m/d/Y', $arrivalDateFrom));
        }

        if ($arrivalDateTo) {
            $query = $query->whereDate('arrival_date', '<=', Carbon::createFromFormat('m/d/Y', $arrivalDateTo));
        }

        if ($shippingMethodId) {
            $query = $query->where('shipping_method_id', $shippingMethodId);
        }

        // $_trucks = $query
        //     ->orderBy('current_location_id', 'ASC')
        //     ->orderBy('id', 'DESC')
        //     ->get();

        // $location = Location::find($currentLocationId);

        // if (optional($location)->name === 'Kho Việt Nam') {
        //     $_trucks = $_trucks->filter(function ($t) {
        //         return $t->debt > 1000;
        //     });
        // }

        // $_trucks = $_trucks->sortByDesc('debt');

        // $paginate = Truck::whereIn('id', Truck::take($_trucks->count())->get()->pluck('id'))->paginate();

        // $trucks = $_trucks
        //     ->skip(($page - 1) * 15)
        //     ->take(15);

        $trucks = $query
            ->orderBy('current_location_id', 'ASC')
            ->orderBy('id', 'DESC')
            ->paginate();

        $allTrucks = Truck::with([
            'departureLocation',
            'currentLocation',
            // 'customers.orders',
            'orders' => function ($q) {
                $q->with([
                    'packs',
                    'payments',
                ]);
            },
        ])
            ->whereIn('id', $incompletedTruckIds)
            ->whereHas('currentLocation', function ($query) {
                $query
                    ->where('type', Location::TRANSSHIPMENT)
                    ->orWhere('type', Location::IN_CHINA);
            })
            ->get();

        $trucksVN = Truck::with([
            'departureLocation',
            'currentLocation',
            'orders' => function ($q) {
                $q->with([
                    'packs',
                    'payments',
                ]);
            },
        ])
            ->whereIn('id', $incompletedTruckIds)
            ->whereHas('currentLocation', function ($query) {
                $query
                    ->where('type', Location::IN_VIETNAM)
                    ->orWhere('name', Location::VIETNAM_INVENTORY)
                    ->orWhere('name', Location::VIETNAM_INVENTORY_2);
            })
            ->get();

        return view('app.truck.index', [
            'locations' => $locations,
            'shippingMethods' => $shippingMethods,

            'inChinaLocations' => $inChinaLocations,
            'transshipmentPoints' => $transshipmentPoints,
            'withoutTransshipmentPoints' => $withoutTransshipmentPoints,
            'inVietnamLocations' => $inVietnamLocations,
            'trucksVN' => $trucksVN,

            // 'paginate' => $paginate,
            'trucks' => $trucks,
            'allTrucks' => $allTrucks,
        ]);
    }

    public function getCompleted(Request $request)
    {
        $incompletedTruckIds = json_decode(Cache::get('incompletedTruckIds', json_encode([])));

        $locations = Location::all();
        $shippingMethods = ShippingMethod::all();

        $inChinaLocations = Location::with([
            'trucks'
        ])
            ->where('type', Location::IN_CHINA)
            ->get();

        $transshipmentPoints = Location::with([
            'trucks'
        ])
            ->where('type', Location::TRANSSHIPMENT)
            ->get();

        $withoutTransshipmentPoints = Location::with([
            'trucks'
        ])
            ->where('type', '!=', Location::TRANSSHIPMENT)
            ->get();

        $inVietnamLocations = Location::with([
            'trucks'
        ])
            ->where('type', Location::IN_VIETNAM)
            ->get();

        $query = Truck::with([
            'departureLocation',
            'currentLocation',
            // 'customers.orders.packs',
            // 'customers.orders.payments',

            'orders' => function ($q) {
                $q->with([
                    'packs',
                    'payments',
                ]);
            },
        ])->whereNotIn('id', $incompletedTruckIds);

        $name = $request->get('name');
        $departureLocationId = $request->get('departure_location_id');
        $currentLocationId = $request->get('current_location_id');
        $departureDateFrom = $request->get('departure_date_from');
        $departureDateTo = $request->get('departure_date_to');
        $arrivalDateFrom = $request->get('arrival_date_from');
        $arrivalDateTo = $request->get('arrival_date_to');
        $shippingMethodId = $request->get('shipping_method_id');
        $page = $request->get('page', 1);

        if ($name) {
            $query = $query->where('name', 'LIKE', "%$name%");
        }

        if ($departureLocationId) {
            $query = $query->whereHas('departureLocation', function ($q) use ($departureLocationId) {
                $q->where('id', $departureLocationId);
            });
        }

        if ($currentLocationId) {
            $query = $query->where('current_location_id', $currentLocationId);
        }

        if ($departureDateFrom) {
            $query = $query->whereDate('departure_date', '>=', Carbon::createFromFormat('m/d/Y', $departureDateFrom));
        }

        if ($departureDateTo) {
            $query = $query->whereDate('departure_date', '<=', Carbon::createFromFormat('m/d/Y', $departureDateTo));
        }

        if ($arrivalDateFrom) {
            $query = $query->whereDate('arrival_date', '>=', Carbon::createFromFormat('m/d/Y', $arrivalDateFrom));
        }

        if ($arrivalDateTo) {
            $query = $query->whereDate('arrival_date', '<=', Carbon::createFromFormat('m/d/Y', $arrivalDateTo));
        }

        if ($shippingMethodId) {
            $query = $query->where('shipping_method_id', $shippingMethodId);
        }

        // $_trucks = $query
        //     ->orderBy('current_location_id', 'ASC')
        //     ->orderBy('id', 'DESC')
        //     ->get();

        // $location = Location::find($currentLocationId);

        // if (optional($location)->name === 'Kho Việt Nam') {
        //     $_trucks = $_trucks->filter(function ($t) {
        //         return $t->debt > 1000;
        //     });
        // }

        // $_trucks = $_trucks->sortByDesc('debt');

        // $paginate = Truck::whereIn('id', Truck::take($_trucks->count())->get()->pluck('id'))->paginate();

        // $trucks = $_trucks
        //     ->skip(($page - 1) * 15)
        //     ->take(15);

        $trucks = $query
            ->orderBy('current_location_id', 'ASC')
            ->orderBy('id', 'DESC')
            ->paginate();

        $allTrucks = Truck::with([
            'departureLocation',
            'currentLocation',
            // 'customers.orders',
            'orders' => function ($q) {
                $q->with([
                    'packs',
                    'payments',
                ]);
            },
        ])
            ->whereHas('currentLocation', function ($query) {
                $query
                    ->where('type', Location::TRANSSHIPMENT)
                    ->orWhere('type', Location::IN_CHINA);
            })
            ->get();

        $trucksVN = Truck::with([
            'departureLocation',
            'currentLocation',
            'orders' => function ($q) {
                $q->with([
                    'packs',
                    'payments',
                ]);
            },
        ])
            ->whereHas('currentLocation', function ($query) {
                $query
                    ->where('type', Location::IN_VIETNAM)
                    ->orWhere('name', Location::VIETNAM_INVENTORY)
                    ->orWhere('name', Location::VIETNAM_INVENTORY_2);
            })
            ->get();

        return view('app.truck.completed', [
            'locations' => $locations,
            'shippingMethods' => $shippingMethods,

            'inChinaLocations' => $inChinaLocations,
            'transshipmentPoints' => $transshipmentPoints,
            'withoutTransshipmentPoints' => $withoutTransshipmentPoints,
            'inVietnamLocations' => $inVietnamLocations,
            'trucksVN' => $trucksVN,

            // 'paginate' => $paginate,
            'trucks' => $trucks,
            'allTrucks' => $allTrucks,
        ]);
    }

    public function create(Request $request)
    {
        Gate::authorize('has-permissions', 'create.truck');

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'max:255',
                Rule::unique('trucks')->whereNull('deleted_at')
            ],
            'shipping_method_id' => [
                'nullable',
                'max:255',
                'integer',
                Rule::exists('shipping_methods', 'id')->whereNull('deleted_at')
            ],
            'departure_location_id' => [
                'required',
                'max:255',
                'integer'
            ],
            'departure_date' => [
                'required',
                'date',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('trucks#create-truck')
                ->withErrors($validator)
                ->withInput();
        }

        $data['current_location_id'] = $data['departure_location_id'];
        $data['cost'] = [
            [
                'ordinal' => 'A',
                'content' => 'Chi phí',
                'amount' => null,
                'note' => null,
                'costs' => [
                    [
                        'ordinal' => 'I',
                        'content' => 'Chi phí bến bãi, vận chuyển',
                        'amount' => null,
                        'note' => null,
                        'costs' => [
                            [
                                'ordinal' => 1,
                                'content' => 'Cơi bạt',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => 2,
                                'content' => 'Đk Lxe',
                                'amount' => null,
                                'note' => null,
                            ],
                            [
                                'ordinal' => 3,
                                'content' => 'Thuê lxe',
                                'amount' => null,
                                'note' => null,
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Bốc',
                                'amount' => null,
                                'note' => 'Có hóa đơn',
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Cẩu',
                                'amount' => null,
                                'note' => null,
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Nâng',
                                'amount' => null,
                                'note' => 'Có hóa đơn',
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Palet',
                                'amount' => null,
                                'note' => 'Có hóa đơn',
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Bd nâng',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => 4,
                                'content' => 'Bd bốc',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => 5,
                                'content' => 'Biên phòng',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => 6,
                                'content' => 'Thuê xe',
                                'amount' => null,
                                'note' => 'Có hóa đơn',
                            ],
                            [
                                'ordinal' => 7,
                                'content' => 'Kiểm dịch',
                                'amount' => null,
                                'note' => 'Có hóa đơn',
                            ],
                            [
                                'ordinal' => 8,
                                'content' => 'Vé B5',
                                'amount' => null,
                                'note' => 'Có hóa đơn',
                            ],
                            [
                                'ordinal' => 9,
                                'content' => 'Bốc lên xe',
                                'amount' => null,
                                'note' => 'Có hóa đơn',
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Nâng',
                                'amount' => null,
                                'note' => null,
                            ],
                            [
                                'ordinal' => 10,
                                'content' => 'Gửi kho',
                                'amount' => null,
                                'note' => null,
                            ],
                        ]
                    ],
                    [
                        'ordinal' => 'II',
                        'content' => 'Chi phí mở tờ khai',
                        'amount' => null,
                        'note' => null,
                        'costs' => [
                            [
                                'ordinal' => 1,
                                'content' => 'Tiếp nhận',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => 2,
                                'content' => 'Kiểm hóa',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => 3,
                                'content' => 'Kiểm hóa',
                                'amount' => null,
                                'note' => 'Tách hồ sơ',
                            ],
                            [
                                'ordinal' => 4,
                                'content' => null,
                                'amount' => null,
                                'note' => null,
                            ],
                            [
                                'ordinal' => 5,
                                'content' => null,
                                'amount' => null,
                                'note' => null,
                            ],
                            [
                                'ordinal' => 6,
                                'content' => null,
                                'amount' => null,
                                'note' => null,
                            ],
                        ],
                    ],
                    [
                        'ordinal' => 'III',
                        'content' => 'Chi phí luật và phát sinh',
                        'amount' => null,
                        'note' => null,
                        'costs' => [
                            [
                                'ordinal' => 1,
                                'content' => 'Dốc quýt',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => 2,
                                'content' => 'Hq b5',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Mái che',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Muộn',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Lấy c/o gửi sang hữu nghị',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                            [
                                'ordinal' => null,
                                'content' => 'Kéo mooc lxct',
                                'amount' => null,
                                'note' => 'Không hóa đơn',
                            ],
                        ],
                    ],
                ]
            ],
            [
                'ordinal' => 'B',
                'content' => 'Thuế',
                'amount' => null,
                'note' => null,
                'costs' => [
                    [
                        'ordinal' => 1,
                        'content' => 'Tờ khai',
                        'amount' => null,
                        'note' => null,
                    ],
                    [
                        'ordinal' => 2,
                        'content' => 'Luật',
                        'amount' => null,
                        'note' => null,
                    ],
                ],
            ],
            [
                'ordinal' => 'C',
                'content' => 'Dịch vụ đầu trung',
                'amount' => null,
                'note' => null,
                'costs' => [
                    [
                        'ordinal' => 1,
                        'content' => 'Dịch vụ',
                        'amount' => null,
                        'note' => 'Dự kiến',
                    ],
                    [
                        'ordinal' => 2,
                        'content' => 'Tiền thuê lái xe, vận tàu và lưu xe',
                        'amount' => null,
                        'note' => 'Dự kiến',
                    ],
                    [
                        'ordinal' => 3,
                        'content' => 'Mua đường',
                        'amount' => null,
                        'note' => 'Dự kiến',
                    ],
                    [
                        'ordinal' => null,
                        'content' => 'bốc xếp',
                        'amount' => null,
                        'note' => null,
                    ],
                    [
                        'ordinal' => null,
                        'content' => 'Chi phí tq',
                        'amount' => null,
                        'note' => null,
                    ],
                    [
                        'ordinal' => null,
                        'content' => 'Dự trù trả hàng',
                        'amount' => null,
                        'note' => null,
                    ],
                    [
                        'ordinal' => null,
                        'content' => 'Hạ hàng',
                        'amount' => null,
                        'note' => null,
                    ],
                    [
                        'ordinal' => null,
                        'content' => 'Tiền xe',
                        'amount' => null,
                        'note' => null,
                    ],
                ],
            ],
        ];
        Truck::create($data);

        return redirect()
            ->route('truck.index');
    }

    public function show(Truck $truck)
    {
        $locations = Location::all();
        $shippingMethods = ShippingMethod::all();

        $customers = Customer::orderBy('id', 'DESC')->get();
        $orders = Order::with([
            'packs',
            'payments',
            'declarations',
            'truck',
            'customer',
        ])
            ->where('location_id', optional($truck->currentLocation)->id)
            ->whereDoesntHave('truck')
            ->orderBy('id', 'DESC')
            ->get();

        $truck->loadMissing([
            'departureLocation',
            'currentLocation',
            'orders' => function ($q) {
                $q
                    ->with([
                        'packs',
                        'payments',
                        'declarations',
                        'truck',
                        'customer',
                    ])
                    ->withCount('packs')
                    // ->orderBy('added_to_truck_at');
                ;
            },
        ]);

        $truck->orders = $truck
            ->orders
            ->sortBy('customer.name');

        return view('app.truck.show', [
            'locations' => $locations,
            'shippingMethods' => $shippingMethods,
            'truck' => $truck,
            'customers' => $customers,
            'orders' => $orders,
        ]);
    }

    public function update(Request $request, Truck $truck)
    {
        if (isSeller()) return redirect()
            ->back();

        DB::beginTransaction();

        $data = $request->all();

        $validator = Validator::make($data, [
            'name' => [
                'required',
                'max:255',
                Rule::unique('trucks')->ignore($truck->id, 'id')
            ],
            'shipping_method_id' => [
                'nullable',
                'max:255',
                'integer',
                Rule::exists('shipping_methods', 'id')->whereNull('deleted_at')
            ],
            'departure_location_id' => [
                'required',
                'integer'
            ],
            'current_location_id' => [
                'required',
                'integer'
            ],
            'departure_date' => [
                'required',
                'date',
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->to('trucks/' .  $truck->id . '#update-truck')
                ->withErrors($validator)
                ->withInput();
        }

        $truck->fill($data);
        $truck->save();

        foreach ($truck->getChanges() as $attribute => $value) {
            if ($attribute !== 'updated_at') {
                Gate::authorize('has-permissions', 'update.truck.' . $attribute);
            }
        }

        DB::commit();

        return redirect()
            ->back();
    }

    public function updateLocation(Request $request, Truck $truck)
    {
        $data = $request->all();
        $currentLocationId = $data['current_location_id'];
        $truck->current_location_id = $currentLocationId;
        $truck->save();

        return response()
            ->json($truck);
    }

    public function destroy(Truck $truck)
    {
        $truck->delete();

        return redirect()->back();
    }

    public function deleteOrder(Order $order)
    {
        $order->truck_id = null;
        $order->save();

        return redirect()->back();
    }

    public function addOrder(Request $request, Truck $truck)
    {
        Gate::authorize('has-permissions', 'update.order.truck_id');

        if ($truck->at_transshipment_point) {
            return redirect()
                ->back()
                ->with('message', 'Xe đang ở điểm trung chuyển!')
                ->with('alert-class', 'alert-danger');
        }

        $data = $request->all();

        $validator = Validator::make($data, [
            'order_id' => [
                'required',
                Rule::exists('orders', 'id')
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with('message', $validator->errors()->first())
                ->with('alert-class', 'alert-danger');
        }

        $order = $truck->orders()
            ->where('orders.id', $data['order_id'])
            ->first();

        if ($order) {
            return redirect()
                ->back()
                ->with('message', 'Vận đơn đã tồn tại!')
                ->with('alert-class', 'alert-danger');
        }

        $_order = Order::find($data['order_id']);
        $_order->added_to_truck_at = now();
        $_order->save();
        $truck->orders()->save($_order);

        return redirect()
            ->back()
            ->with('message', 'Thêm vận đơn thành công!')
            ->with('alert-class', 'alert-success');
    }

    public function addOrders(Request $request, Truck $truck)
    {
        Gate::authorize('has-permissions', 'update.order.truck_id');

        $data = $request->all();

        $validator = Validator::make($data, [
            'order_ids' => [
                'required',
                Rule::exists('orders', 'id')
            ],
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->with('message', $validator->errors()->first())
                ->with('alert-class', 'alert-danger');
        }

        $order = $truck->orders()
            ->whereIn('orders.id', $data['order_ids'])
            ->first();

        if ($order) {
            return redirect()
                ->back()
                ->with('message', 'Vận đơn đã tồn tại!')
                ->with('alert-class', 'alert-danger');
        }

        foreach ($data['order_ids'] as $id) {
            $_order = Order::find($id);
            $_order->added_to_truck_at = now();
            $_order->save();
            $truck->orders()->save($_order);
        }

        return redirect()
            ->back()
            ->with('message', 'Thêm vận đơn thành công!')
            ->with('alert-class', 'alert-success');
    }
}
