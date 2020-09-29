<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDispatchDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // for the products with variations and dispatched
        // for product remaining combination calculation
        Schema::create('product_dispatch_details', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku', 100); // depending on product code and variation combinations, scanned/auto-generated
            $table->unsignedMediumInteger('quantity')->default(100); // how many of this variation has been released
            $table->unsignedInteger('product_combination_id');  // how many of this product has been released
            $table->unsignedInteger('product_dispatch_id');
        });
        // 'available_quantity' of product_combinations table should be updated after every row 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_dispatch_details');
    }
}
