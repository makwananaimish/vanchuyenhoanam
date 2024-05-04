<?php

use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // factory(App\User::class, 1)->create();

        User::updateOrCreate([
            'email' => 'admin@example.com',
        ], [
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'permissions' => [
                "create.truck" => "1",
                "update.truck.name" => "1",
                "update.truck.departure_location_id" => "1",
                "update.truck.current_location_id" => "1",
                "update.truck.departure_date" => "1",
                "update.truck.arrival_date" => "1",
                "update.truck.cost" => "1",

                "create.customer" => "1",
                "update.customer.name" => "1",
                "update.customer.code" => "1",
                "update.customer.phone" => "1",
                "update.customer.password" => "1",

                "create.order" => "1",
                "update.order.customer_id" => "1",
                "update.order.truck_id" => "1",
                "update.order.code" => "1",
                "update.order.bill" => "1",
                "update.order.product_name" => "1",
                "update.order.weight" => "1",
                "update.order.taxes" => "1",
                "update.order.cost_china" => "1",
                "update.order.cost_vietnam" => "1",
                "update.order.fare_unit_by_weight" => "1",
                "update.order.fare_unit_by_cubic_meters" => "1",
                "update.order.note" => "1",

                "update.order.calculate_cost" => "1"
            ],
        ]);

        User::updateOrCreate([
            'email' => 'manager@example.com',
        ], [
            'name' => 'Manager',
            'email' => 'manager@example.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'permissions' => [
                "create.truck" => "1",
                "update.truck.name" => "1",
                "update.truck.departure_location_id" => "1",
                "update.truck.current_location_id" => "1",
                "update.truck.departure_date" => "1",
                "update.truck.arrival_date" => "1",
                "update.truck.cost" => "1",

                "create.customer" => "1",
                "update.customer.name" => "1",
                "update.customer.code" => "1",
                "update.customer.phone" => "1",
                "update.customer.password" => "1",

                "create.order" => "1",
                "update.order.customer_id" => "1",
                "update.order.truck_id" => "1",
                "update.order.code" => "1",
                "update.order.bill" => "1",
                "update.order.product_name" => "1",
                "update.order.weight" => "1",
                "update.order.taxes" => "1",
                "update.order.cost_china" => "1",
                "update.order.cost_vietnam" => "1",
                "update.order.fare_unit_by_weight" => "1",
                "update.order.fare_unit_by_cubic_meters" => "1",
                "update.order.note" => "1",

                "update.order.calculate_cost" => "0"
            ],
        ]);
    }
}
