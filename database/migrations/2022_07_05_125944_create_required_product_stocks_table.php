<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_product_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('selected_stock_code');
            $table->unsignedMediumInteger('quantity');      // deducted quantity
            $table->unsignedInteger('required_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_product_stocks');
    }
}
