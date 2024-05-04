<?php

use App\Option;
use Illuminate\Database\Seeder;

class OptionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Option::firstOrCreate([
            'name' => 'rmb_to_vnd'
        ], [
            'value' => 3500
        ]);

        Option::firstOrCreate([
            'name' => 'outcome_weight'
        ], [
            'value' => 1000000
        ]);
    }
}
