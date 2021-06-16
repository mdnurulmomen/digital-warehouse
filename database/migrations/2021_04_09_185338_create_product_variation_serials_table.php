<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationSerialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variation_serials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_no');
            $table->boolean('has_requisitions')->default(false);
            $table->boolean('has_dispatched')->default(false);
            $table->unsignedInteger('merchant_product_variation_id');
            $table->unsignedInteger('product_variation_stock_id');
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
        Schema::dropIfExists('product_variation_serials');
    }
}
