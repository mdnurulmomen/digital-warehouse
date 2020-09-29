<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackedDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('packed_dispatches', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('quantity')->default(10); // quantity of packages needed
            $table->unsignedInteger('product_dispatch_id');
            $table->unsignedSmallInteger('packaging_package_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packed_dispatches');
    }
}
