<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseContainerShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_container_shelves', function (Blueprint $table) {
            $table->increments('id');
            // warhousName-containerTypePrefix-numberContainer-shelfNumber (auto-generated)
            // $table->string('name', 100); 
            // $table->unsignedSmallInteger('storing_price')->default(0);
            // $table->unsignedSmallInteger('selling_price')->default(0);
            // $table->boolean('has_units')->default(0);
            // $table->boolean('engaged')->default(0);
            $table->unsignedInteger('warehouse_container_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_container_shelves');
    }
}
