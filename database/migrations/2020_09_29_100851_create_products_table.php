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
            $table->string('name', 100); // polo t-shirt / fair n lovely
            $table->string('sku', 100); // scanned or generated on product-code & merchant code
            $table->unsignedMediumInteger('price')->default(100); // minimum one if has variations
            $table->unsignedMediumInteger('available_quantity')->default(100); // default 100 as initial storing default is 100 (quantity including all variations)
            $table->string('description')->nullable(); // short description what has been stored (may be hints for identification)
            $table->boolean('has_variations')->default(0);
            $table->unsignedSmallInteger('product_category_id'); // stationary / garments
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
