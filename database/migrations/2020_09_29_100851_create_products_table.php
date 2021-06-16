<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name'); // polo t-shirt / fair n lovely
            $table->string('description')->nullable(); // short description about what has been stored (may be hints for identification / bulk products)
            $table->string('quantity_type')->default('box');  // kg / meter / pc's
            $table->boolean('has_variations')->default(false);
            $table->boolean('has_serials')->default(false);
            $table->unsignedSmallInteger('product_category_id')->nullable()->default(0); // stationary / garments
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
