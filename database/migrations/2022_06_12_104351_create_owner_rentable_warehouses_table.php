<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOwnerRentableWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('owner_rentable_warehouses', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedTinyInteger('number_rentable_warehouses');
            $table->string('available_size');
            $table->tinyInteger('status')->default(0); // 1 for not reviewed / 0 for reviewed / -1 for cancelled
            $table->unsignedSmallInteger('warehouse_owner_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('owner_rentable_warehouses');
    }
}
