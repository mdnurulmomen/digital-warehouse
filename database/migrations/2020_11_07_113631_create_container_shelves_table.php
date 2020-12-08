<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainerShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('container_shelves', function (Blueprint $table) {
            $table->smallIncrements('id');
            // $table->string('name', 100);
            // $table->string('code', 100);
            $table->unsignedTinyInteger('quantity')->default(10); // in each container
            // $table->unsignedSmallInteger('storing_price')->default(100);  // default price
            // $table->unsignedSmallInteger('selling_price')->default(100);  // default price
            $table->boolean('has_units')->default(false); // each shelf has units
            $table->unsignedInteger('container_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('container_shelves');
    }
}
