<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku', 100); // scanned or generated on product-code & merchant code
            $table->unsignedMediumInteger('initial_quantity')->default(100); 
            $table->unsignedMediumInteger('available_quantity')->default(100);
            // $table->unsignedMediumInteger('price')->default(100);
            $table->unsignedDecimal('price', $precision = 8, $scale = 2);
            $table->unsignedSmallInteger('variation_id'); // stationary / garments
            $table->unsignedInteger('product_id'); // stationary / garments
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_variations');
    }
}
