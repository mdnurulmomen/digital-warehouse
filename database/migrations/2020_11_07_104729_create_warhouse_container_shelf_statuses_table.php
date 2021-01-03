<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseContainerShelfStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warhouse_container_shelf_statuses', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            // $table->boolean('hired')->default(false);
            $table->unsignedDecimal('engaged', $precision = 2, $scale = 1)->default(0); // 1 for full engaged, .5 for partial engaged
            $table->unsignedInteger('warhouse_container_id');
            $table->unsignedInteger('warhouse_container_status_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warhouse_container_shelf_statuses');
    }
}
