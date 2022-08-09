<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantPaymentFragmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_payment_fragments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('invoice_no');
            $table->unsignedMediumInteger('paid_amount')->default(0); // paid rent of total_rent
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
        Schema::dropIfExists('merchant_payment_fragments');
    }
}
