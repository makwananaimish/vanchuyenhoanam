<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopSellersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('top_sellers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('month');
            $table->unsignedBigInteger('year');
            $table->decimal('commission', 30, 10);
            $table->unsignedBigInteger('seller_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('top_sellers');
    }
}
