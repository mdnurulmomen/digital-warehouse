<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // first step to store at any warhouse
        Schema::create('merchant_deals', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('expired_at')->useCurrent();
            $table->unsignedMediumInteger('paid_amount')->default(0); // paid price for this product to warhouse authority
            // $table->boolean('e_commerce_fulfillment')->default(0); // whether merchant wants to delvery services or not
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
        Schema::dropIfExists('merchant_deals');
    }
}
