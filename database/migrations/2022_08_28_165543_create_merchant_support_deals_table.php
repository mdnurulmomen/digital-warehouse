<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantSupportDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_support_deals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->unsignedTinyInteger('sale_percentage')->default(0);  // if e-commerce-fulfillment enabled
            $table->boolean('e_commerce_fulfillment_support')->default(false); // whether merchant wants to delvery services or not
            $table->unsignedMediumInteger('e_commerce_fulfillment_charge')->default(0);  // if e-commerce-fulfillment enabled
            $table->boolean('purchase_support')->default(false);
            $table->unsignedMediumInteger('purchase_support_charge')->default(0);
            $table->boolean('pos_support')->default(false);
            $table->unsignedMediumInteger('pos_support_charge')->default(0);
            $table->unsignedTinyInteger('number_outlets')->default(0);  // if pos_support enabled
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
        Schema::dropIfExists('merchant_support_deals');
    }
}
