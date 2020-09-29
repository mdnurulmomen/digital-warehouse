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
        // should update warhouse_container_shelf_units / warhouse_container_shelfs / warhouse_containers when it is included in dealt_spaces
        // should release related spaces when merchant_deal is expired
        Schema::create('dealt_spaces', function (Blueprint $table) {
            $table->increments('id');
            $table->string('space_type', 100);  // warhouse_container_shelf_units / warhouse_container_shelfs / warhouse_container_shelf_units
            $table->unsignedInteger('space_id');
            $table->unsignedInteger('merchant_deal_id');
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
