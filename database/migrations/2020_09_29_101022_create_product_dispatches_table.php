<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // all product dispatched in a single dispatch
        // for all products of every type dispatched related dispatch record
        // space release should be a field when releasing products which should be filled with scan
        Schema::create('product_dispatches', function (Blueprint $table) {
            $table->increments('id');
            // $table->string('sku', 100); // scanned from product / box or searched out
            $table->unsignedSmallInteger('quantity')->default(100); // how many of this product with all variatons have been released / for the products that have no variation
            // $table->boolean('with_packaging')->default(0);
            $table->boolean('has_variations')->default(0);
            $table->unsignedInteger('product_id');
            $table->unsignedInteger('dispatch_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_dispatches');
    }
}
