<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderDeclarationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order_declarations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->string('code')->nullable()->comment('Mã sản phẩm');
            $table->longText('images')->nullable()->comment('Ảnh sản phẩm, cho up 3 ảnh');
            $table->string('name')->nullable()->comment('Tên sản phẩm, dùng để làm gì');

            $table->float('length')->nullable()->comment('Chiều dài sản phẩm');
            $table->float('width')->nullable()->comment('Chiều rộng sản phẩm');
            $table->float('height')->nullable()->comment('Chiều cao sản phẩm');
            $table->string('size')->nullable()->comment('Kích thước sản phẩm');

            $table->string('brand')->nullable()->comment('Thương hiệu, kí hiệu trên sản phẩm');
            $table->string('material')->nullable()->comment('Chất liệu');
            $table->float('weight_per_product')->nullable()->comment('Cân nặng 1 sản phẩm');
            $table->integer('quantity_per_pack')->nullable()->comment('Số sản phẩm/kiện,bao.');
            $table->integer('pack_quantity')->nullable()->comment('Số kiện');
            $table->integer('quantity')->nullable()->comment('Tổng số sản phẩm');
            $table->string('voltage_power_parameters')->nullable()->comment('Điện áp, công suất, thông số');
            $table->float('weight_per_box')->nullable()->comment('Cân nặng 1 thùng');

            $table->float('box_length')->nullable()->comment('Chiều dài thùng');
            $table->float('box_width')->nullable()->comment('Chiều rộng thùng');
            $table->float('box_height')->nullable()->comment('Chiều cao thùng');
            $table->float('box_size')->nullable()->comment('Kích thước thùng');

            $table->float('cubic_meters')->nullable()->comment('Tổng số m3');
            $table->float('weight')->nullable()->comment('Tổng số kg');

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
        Schema::dropIfExists('order_declarations');
    }
}
