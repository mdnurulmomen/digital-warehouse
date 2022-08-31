<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantSupportRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_support_rents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedMediumInteger('e_commerce_fulfillment_charge')->default(0);  // if e-commerce-fulfillment enabled then current rent of fulfillment support
            $table->unsignedMediumInteger('purchase_support_charge')->default(0);  // if e-commerce-fulfillment enabled then current rent of fulfillment support
            $table->unsignedMediumInteger('pos_support_charge')->default(0);  // if e-commerce-fulfillment enabled then current rent of fulfillment support
            $table->unsignedInteger('merchant_rent_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_support_rents');
    }
}
