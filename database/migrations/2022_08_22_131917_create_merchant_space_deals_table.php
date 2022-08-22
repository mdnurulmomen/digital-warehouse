<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantSpaceDealsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // first step to store at any warehouse
        Schema::create('merchant_space_deals', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->boolean('active')->default(1); // whether deal is still active or not
            $table->boolean('auto_renewal')->default(true); // If auto-renew, generate bill for next month automatically
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
        Schema::dropIfExists('merchant_space_deals');
    }
}
