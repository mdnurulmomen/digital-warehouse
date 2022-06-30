<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseContainerStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_container_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            // $table->boolean('hired')->default(false);
            $table->float('engaged', $precision = 2, $scale = 1)->default(0);  // 1 for full engaged, .5 for partial engaged (rented)
            $table->float('occupied', $precision = 2, $scale = 1)->default(0);  // 1 for full, .5 for partial used
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
        Schema::dropIfExists('warehouse_container_statuses');
    }
}
