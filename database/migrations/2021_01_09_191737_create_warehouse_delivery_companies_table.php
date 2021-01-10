<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseDeliveryCompaniesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_delivery_companies', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('warehouse_id');
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
        Schema::dropIfExists('warehouse_delivery_companies');
    }
}
