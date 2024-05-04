<?php

use App\Truck;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;

class TrucksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i = 0; $i < 50; $i++) {
            $prefixNum = Arr::random([29, 30, 31, 32, 33, 40]);
            $prefixSymbol = 'C' . Arr::random([1, 2, 3]);
            $suffixNum = random_int(10000, 99999);
            $name = $prefixNum . $prefixSymbol . '-' . $suffixNum;
            $departureLocationId = Arr::random([1, 2, 3]);
            $currentLocationId = $departureLocationId;
            $departureDate = Carbon::today()
                ->subMonth(rand(0, 12))
                ->subDays(rand(0, 30))
                ->format('Y-m-d');
            $cost = [
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

            Truck::create([
                'name' => $name,
                'departure_location_id' => $departureLocationId,
                'current_location_id' => $currentLocationId,
                'departure_date' => $departureDate,
                'arrival_date' => null,
                'cost' => $cost,
            ]);
        }
    }
}
