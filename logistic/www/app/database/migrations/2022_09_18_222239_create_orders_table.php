<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('truck_id')->nullable();
            $table->string('code')->unique();
            $table->string('bill')->unique();
            $table->string('product_name')->nullable();
            $table->float('weight')->nullable();
            $table->decimal('taxes', 20, 0)->default(0);
            $table->decimal('cost_china', 20, 0)->default(0);
            $table->decimal('cost_vietnam', 20, 0)->default(0);
            $table->decimal('fare_unit_by_weight', 20, 0);
            $table->decimal('fare_unit_by_cubic_meters', 20, 0);
            $table->string('note')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('orders');
    }
}
