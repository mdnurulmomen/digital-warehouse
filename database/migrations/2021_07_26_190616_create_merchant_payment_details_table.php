<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantPaymentDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_payment_details', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('issued_from')->useCurrent();
            $table->timestamp('expired_at')->useCurrent();
            $table->unsignedMediumInteger('rent')->default(0); // current rent of space to calculate total rent
            $table->unsignedInteger('dealt_space_id');
            $table->unsignedInteger('merchant_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_payment_details');
    }
}
