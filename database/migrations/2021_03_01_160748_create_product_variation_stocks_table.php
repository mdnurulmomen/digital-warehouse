<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variation_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedMediumInteger('stock_quantity');
            $table->unsignedMediumInteger('available_quantity');
            $table->unsignedDecimal('unit_buying_price', $precision = 8, $scale = 2)->default(0);
            // $table->boolean('has_serials')->default(false);
            $table->unsignedMediumInteger('merchant_product_variation_id'); // stationary / garments
            $table->unsignedInteger('product_stock_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variation_stocks');
    }
}
