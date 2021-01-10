<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseContainerShelfUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_container_shelf_units', function (Blueprint $table) {
            $table->increments('id');
            // warhousName-containerTypePrefix-numberContainer-shelfNumber-unitNumber
            // $table->string('name', 100);
            // $table->unsignedSmallInteger('length')->default(100);
            // $table->unsignedSmallInteger('depth')->default(100);
            // $table->unsignedSmallInteger('height')->default(100);
            // $table->unsignedSmallInteger('storing_price')->default(0);  // price for each unit
            // $table->unsignedSmallInteger('selling_price')->default(0);  // price for each unit
            // $table->boolean('engaged')->default(0);
            // $table->unsignedInteger('warehouse_container_shelf_id');
            $table->unsignedInteger('warehouse_container_shelf_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_container_shelf_units');
    }
}
