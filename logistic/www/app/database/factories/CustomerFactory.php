<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Customer;
use Faker\Generator as Faker;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Hash;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'name' =>  $faker->name,
        'code' => 'KH-' . random_int(10000, 99999),
        'phone' => Arr::random(['032', '033', '034', '035', '036', '037', '038', '039']) .  random_int(1000000, 9999999),
        'password' => Hash::make('123456')
    ];
});
