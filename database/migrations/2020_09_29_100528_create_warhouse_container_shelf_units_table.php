<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseContainerShelfUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // units per shelf (should be engaged along with related engaged shelf) 
        Schema::create('warhouse_container_shelf_units', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100); // warhousName-containerTypePrefix-numberContainer-shelfNumber-unitNumber
            $table->unsignedSmallInteger('length')->default(100);
            $table->unsignedSmallInteger('depth')->default(100);
            $table->unsignedSmallInteger('height')->default(100);
            $table->unsignedSmallInteger('storing_price')->default(0);  // price for each unit
            $table->unsignedSmallInteger('selling_price')->default(0);  // price for each unit
            $table->boolean('engaged')->default(0);
            $table->unsignedInteger('warhouse_container_shelf_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warhouse_container_shelf_units');
    }
}
