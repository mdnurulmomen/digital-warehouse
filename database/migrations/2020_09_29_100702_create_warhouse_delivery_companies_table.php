<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseDeliveryCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // available companies for each warhouse
        Schema::create('warhouse_delivery_companies', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('warhouse_id');
            $table->unsignedSmallInteger('delivery_company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warhouse_delivery_companies');
    }
}
