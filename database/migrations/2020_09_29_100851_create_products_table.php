<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100); // polo t-shirt / fair n lovely
            $table->string('description')->nullable(); // short description what has been stored (may be hints for identification / bulk products)
            $table->string('sku', 100); // scanned or generated on product-code & merchant code
            // $table->unsignedMediumInteger('price')->default(100); // minimum-one if has variations, 0 for bulk items
            $table->unsignedDecimal('price', $precision = 8, $scale = 2);
            $table->string('quantity_type', 100)->default('box');  // kg / meter / pc's
            $table->boolean('has_variations')->default(0);
            $table->unsignedSmallInteger('product_category_id')->nullable()->default(0); // stationary / garments
            $table->unsignedInteger('merchant_id'); // who's product is this
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
