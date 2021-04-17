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
            $table->mediumInteger('stock_quantity');
            $table->mediumInteger('available_quantity');
            $table->boolean('has_serials')->default(false);
            $table->unsignedMediumInteger('product_variation_id'); // stationary / garments
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
