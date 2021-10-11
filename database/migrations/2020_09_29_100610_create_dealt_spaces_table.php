<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDealtSpacesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // each of many racks / shelfs / units  hired by merchant
        // should update warehouse_container_shelf_units / warehouse_container_shelfs / warehouse_containers when it is included in dealt_spaces
        // should release related spaces when merchant_deal is expired
        Schema::create('dealt_spaces', function (Blueprint $table) {
            $table->increments('id');
            // $table->unsignedTinyInteger('rent_id');
            // $table->unsignedSmallInteger('rent')->default(0);
            $table->unsignedTinyInteger('rent_period_id');
            $table->string('space_type', 100);  // warehouse_container_shelf_units / warehouse_container_shelfs / warehouse_container_shelf_units
            $table->unsignedInteger('space_id');
            // $table->float('engaged', $precision = 2, $scale = 1)->default(0);  // 1 for full, .5 for partial
            // $table->unsignedSmallInteger('warehouse_id');
            $table->unsignedMediumInteger('warehouse_container_id');
            $table->unsignedInteger('merchant_deal_id');
            // $table->unsignedInteger('merchant_payment_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dealt_spaces');
    }
}
