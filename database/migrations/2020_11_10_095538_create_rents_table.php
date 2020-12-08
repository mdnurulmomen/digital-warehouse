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
            $table->unsignedSmallInteger('storing_price')->default(100);
            $table->unsignedSmallInteger('selling_price')->default(100);
            $table->unsignedTinyInteger('rent_period_id');
            $table->string('warhouse_storer_type', 100);
            $table->unsignedInteger('warhouse_storer_id');
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
