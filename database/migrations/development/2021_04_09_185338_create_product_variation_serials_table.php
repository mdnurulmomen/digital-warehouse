<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductVariationSerialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_variation_serials', function (Blueprint $table) {
            $table->increments('id');
            $table->string('serial_no');
            $table->boolean('has_requisitions')->nullable()->default(false);
            $table->boolean('has_dispatched')->nullable()->default(false);
            $table->unsignedInteger('product_variation_stock_id');
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
        Schema::dropIfExists('product_variation_serials');
    }
}
