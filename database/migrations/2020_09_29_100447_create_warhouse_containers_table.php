<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // for each racks / palletes in every warhouse
        // should be engaged once full rack / container is selected for one product
        Schema::create('warhouse_containers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->unsignedTinyInteger('container_type_id');  // rack / pallet
            $table->unsignedTinyInteger('storage_type_id');  // ex. food & beverage/industrial
            $table->unsignedSmallInteger('length')->default(100);
            $table->unsignedSmallInteger('depth')->default(100);
            $table->unsignedSmallInteger('height')->default(100);
            $table->unsignedSmallInteger('storing_price')->default(0);  // price for storing only
            $table->unsignedSmallInteger('selling_price')->default(0);  // price for store and sell
            $table->boolean('has_shelves')->default(0);
            $table->boolean('engaged')->default(0);
            $table->unsignedInteger('warhouse_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warhouse_containers');
    }
}
