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
        // first step to store at any warehouse
        Schema::create('merchant_deals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->boolean('active')->default(1); // whether deal is still active or not
            $table->boolean('e_commerce_fulfillment')->default(0); // whether merchant wants to delvery services or not
            $table->boolean('auto_renewal')->default(true); // If auto-renew, generate bill for next month automatically  
            $table->unsignedTinyInteger('sale_percentage')->nullable();  // sale percentage if e-commerce-fulfillment enabled
            // $table->unsignedMediumInteger('issued_amount')->default(0); // issued rent to warehouse authority
            // $table->unsignedMediumInteger('paid_amount')->default(0); // paid price for this product to warehouse authority
            $table->unsignedTinyInteger('rent_period_id');
            $table->unsignedInteger('merchant_id');
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
        Schema::dropIfExists('merchant_deals');
    }
}
