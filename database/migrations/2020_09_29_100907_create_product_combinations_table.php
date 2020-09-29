<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCombinationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // for the products with variations
        // for multiple variation_types, product_combination will be multiplication of variations of each variation_types that hold by this product   (9 for 3 & 3 variations of 2 variation_types)
        // updated on every dispatches in product_dispatch_details table
        Schema::create('product_combinations', function (Blueprint $table) {
            $table->increments('id'); // combination_id
            $table->string('sku', 100); // depending on product code and variation combinations
            $table->unsignedMediumInteger('quantity'); // initial quantity ex. red-small t-shirt 5 pics
            $table->unsignedMediumInteger('available_quantity'); // remaining quantities
            $table->unsignedSmallInteger('price');
            $table->unsignedInteger('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_combinations');
    }
}
