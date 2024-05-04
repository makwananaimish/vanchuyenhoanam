<?php

use App\Customer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CustomersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Customer::updateOrCreate([
            'code' => Customer::NONAME_CODE,
        ], [
            'name' => Customer::NONAME_CODE,
            'code' => Customer::NONAME_CODE,
            'phone' => Customer::NONAME_CODE,
            'address' => null,
            'password' => Hash::make('123456'),
        ]);

        Customer::updateOrCreate([
            'code' => Customer::EXPRESS_CODE,
        ], [
            'name' => Customer::EXPRESS_CODE,
            'code' => Customer::EXPRESS_CODE,
            'phone' => Customer::EXPRESS_CODE,
            'address' => null,
            'password' => Hash::make('123456'),
        ]);

        // factory(App\Customer::class, 100)->create();
    }
}
