<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCombinationDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_combination_details', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('variation_id'); // variations (small / red)
            $table->unsignedInteger('product_combination_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_combination_details');
    }
}
