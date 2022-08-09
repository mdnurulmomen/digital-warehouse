<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantDealInstalmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_deal_instalments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedTinyInteger('number_installment'); // number of installment * rent-period (number of days)
            $table->timestamp('date_from')->useCurrent();
            $table->timestamp('date_to')->useCurrent();
            $table->unsignedMediumInteger('total_rent')->default(0); // total rent to warehouse authority
            $table->unsignedTinyInteger('discount')->default(0); // discount percentage to total rent
            // $table->mediumInteger('previous_due')->default(0); // past due amount
            $table->mediumInteger('net_payable')->default(0); // total rent minus discount plus dues
            $table->unsignedMediumInteger('total_paid_amount')->default(0); // paid rent of total_rent
            $table->mediumInteger('current_due')->default(0); // past due amount
            // $table->unsignedMediumInteger('issued_amount')->default(0); // issued rent to warehouse authority
            // $table->unsignedMediumInteger('paid_amount')->default(0); // paid price for this product to warehouse authority
            $table->unsignedInteger('merchant_deal_id');
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
        Schema::dropIfExists('merchant_deal_instalments');
    }
}
