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
            $table->unsignedMediumInteger('stock_quantity')->default(0);
            $table->unsignedMediumInteger('available_quantity')->default(0); 
            // $table->unsignedMediumInteger('total_quantity')->default(0);
            $table->unsignedSmallInteger('product_variation_id'); // stationary / garments
            $table->unsignedInteger('product_stock_id');
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
        Schema::dropIfExists('product_variation_stocks');
    }
}
