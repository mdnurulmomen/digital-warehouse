<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_payments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no');
            $table->mediumInteger('previous_due')->default(0); // past due amount
            $table->unsignedMediumInteger('total_rent')->default(0); // total rent to warehouse authority
            $table->unsignedTinyInteger('discount')->default(0); // discount percentage to total rent
            $table->unsignedMediumInteger('net_payable')->default(0); // total rent minus discount plus dues
            $table->unsignedMediumInteger('paid_amount')->default(0); // paid rent of total_rent
            $table->mediumInteger('current_due')->default(0); // past due amount
            // $table->unsignedMediumInteger('issued_amount')->default(0); // issued rent to warehouse authority
            // $table->unsignedMediumInteger('paid_amount')->default(0); // paid price for this product to warehouse authority
            $table->unsignedInteger('merchant_deal_id');
            $table->timestamp('paid_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_payments');
    }
}
