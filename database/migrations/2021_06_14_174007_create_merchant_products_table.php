<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('sku'); // scanned or generated on product-code & merchant code
            $table->string('preview')->nullable();
            $table->unsignedMediumInteger('manufacturer_id')->nullable();
            $table->string('description')->nullable(); // short description about what has been stored (may be hints for identification / bulk products)
            $table->unsignedSmallInteger('warning_quantity')->default(100);
            $table->unsignedDecimal('discount', $precision = 5, $scale = 2)->default(0.0);
            $table->unsignedDecimal('selling_price', $precision = 8, $scale = 2)->nullable();
            $table->unsignedInteger('product_id'); // which product is this
            $table->unsignedInteger('merchant_id'); // who's product is this
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_products');
    }
}
