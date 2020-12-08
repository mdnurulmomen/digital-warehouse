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
            $table->unsignedSmallInteger('quantity')->default(100);

            // $table->unsignedSmallInteger('container_storing_price')->default(0);    // price for store only
            // $table->unsignedSmallInteger('container_selling_price')->default(0);    // price for store and sell 

            // $table->unsignedSmallInteger('shelf_storing_price')->default(0);        // price for store only
            // $table->unsignedSmallInteger('shelf_selling_price')->default(0);        // price for store and sell 

            // $table->unsignedSmallInteger('unit_storing_price')->default(0);         // price for store only
            // $table->unsignedSmallInteger('unit_selling_price')->default(0);         // price for store and sell 
            
            $table->unsignedSmallInteger('container_id');
            $table->unsignedSmallInteger('warhouse_id');

            $table->softDeletes();
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
