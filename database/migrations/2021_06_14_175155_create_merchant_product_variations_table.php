<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_product_variations', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku'); // scanned or generated on product-code & merchant code
            $table->string('preview')->nullable();
            // $table->string('description')->nullable(); // short description about what has been stored (may be hints for identification / bulk products)
            $table->unsignedMediumInteger('available_quantity')->default(0);
            $table->unsignedDecimal('selling_price', $precision = 8, $scale = 2);
            $table->unsignedSmallInteger('product_variation_id'); // stationary / garments
            $table->unsignedInteger('merchant_product_id'); // stationary / garments
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_product_variations');
    }
}
