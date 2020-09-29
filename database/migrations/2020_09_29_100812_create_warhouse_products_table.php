<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // engaged units / shelfs / racks for any single type product (from->to unit of a specific shelf of a specific rack )
        // should be engaged the related space of related table
        Schema::create('warhouse_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('space_type'); // warhouse_container_shelf_units / warhouse_container_shelfs / warhouse_container_shelf_units
            $table->unsignedInteger('space_id'); // id of engaged space
            $table->unsignedInteger('product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warhouse_products');
    }
}
