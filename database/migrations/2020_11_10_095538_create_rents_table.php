<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('rent')->default(0);
            // $table->unsignedSmallInteger('storing_price')->default(100);
            // $table->unsignedSmallInteger('selling_price')->default(100);
            $table->unsignedTinyInteger('rent_period_id');
            $table->string('warehouse_storer_type', 100);
            $table->unsignedInteger('warehouse_storer_id');
            $table->boolean('active')->default(false);      // whether this rent is in-use or not 
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
        Schema::dropIfExists('rents');
    }
}
