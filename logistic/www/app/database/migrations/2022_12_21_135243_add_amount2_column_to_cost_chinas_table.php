<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAmount2ColumnToCostChinasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cost_chinas', function (Blueprint $table) {
            $table->decimal('amount2', 20, 0)->after('amount')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cost_chinas', function (Blueprint $table) {
            $table->dropColumn('amount2');
        });
    }
}
