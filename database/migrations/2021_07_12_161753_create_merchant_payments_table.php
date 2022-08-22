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
            // $table->mediumInteger('previous_due')->default(0); // past due amount
            $table->unsignedMediumInteger('paid_amount')->default(0); // paid rent of total_rent
            $table->mediumInteger('current_due')->default(0); // past due amount
            $table->timestamp('paid_at')->useCurrent();
            $table->morphs('issuer');
            $table->nullableMorphs('updater');
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
        Schema::dropIfExists('merchant_payments');
    }
}
