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
        // one to many products of each merchant id (storing/initial quantity)
        Schema::create('merchant_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('product_id');
            $table->unsignedMediumInteger('quantity'); // initial quantity of each type product including all variations. Default 100 of quantity-unit
            $table->unsignedTinyInteger('quantity_type_id'); // kg / pc / grams / litre / box / bag / packet
            $table->unsignedInteger('merchant_id');
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
