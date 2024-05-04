<?php

use App\Location;
use Illuminate\Database\Seeder;

class LocationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $locations = [
            [
                'type' => Location::IN_CHINA,
                'name' => 'Bằng Tường',
            ],
            [
                'type' => Location::IN_CHINA,
                'name' => 'Quảng Châu'
            ],
            [
                'type' => Location::IN_CHINA,
                'name' => 'Bắc Kinh'
            ],
            [
                'type' => null,
                'name' => 'Hữu Nghị'
            ],
            [
                'type' => null,
                'name' => 'Kho Việt Nam'
            ],
            [
                'type' => null,
                'name' => 'Hoàn Thành'
            ]
        ];

        foreach ($locations as $location) {
            Location::firstOrCreate([
                'name' => $location['name']
            ], [
                'type' => $location['type'],
                'name' => $location['name'],
            ]);
        }
    }
}
