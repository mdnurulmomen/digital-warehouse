<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseContainerShelvesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warhouse_container_shelves', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);  // warhousName-containerTypePrefix-numberContainer-shelfNumber (auto-generated)
            $table->unsignedSmallInteger('storing_price')->default(0);
            $table->unsignedSmallInteger('selling_price')->default(0);
            $table->boolean('has_units')->default(0);
            $table->boolean('engaged')->default(0);
            $table->unsignedInteger('warhouse_container_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warhouse_container_shelves');
    }
}
