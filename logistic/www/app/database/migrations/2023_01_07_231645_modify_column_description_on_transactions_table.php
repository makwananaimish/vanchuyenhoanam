<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyColumnDescriptionOnTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transactions', function (Blueprint $table) {
            try {
                $table->dropUnique('transactions_description_unique');
            } catch (\Throwable $th) {
                //throw $th;
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transactions', function (Blueprint $table) {
            try {
                $table->unique('description');
            } catch (\Throwable $th) {
                //throw $th;
            }
        });
    }
}
