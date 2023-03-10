<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantSpaceRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_space_rents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedMediumInteger('rent')->default(0); // current rent of space to calculate total rent
            $table->unsignedInteger('dealt_space_id');
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
        Schema::dropIfExists('merchant_space_rents');
    }
}
