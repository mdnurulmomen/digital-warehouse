<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainerShelfUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_shelf_units', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedTinyInteger('quantity')->default(10); // in each container
            // $table->unsignedSmallInteger('storing_price')->default(100);  // default price
            // $table->unsignedSmallInteger('selling_price')->default(100);  // default price
            $table->unsignedInteger('container_shelf_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_shelf_units');
    }
}
