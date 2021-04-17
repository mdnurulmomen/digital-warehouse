<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductStocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_stocks', function (Blueprint $table) {
            $table->increments('id');
            $table->mediumInteger('stock_quantity');    // quantity including all variations
            $table->mediumInteger('available_quantity');
            $table->boolean('has_variations')->default(false);
            $table->boolean('has_serials')->default(false);
            $table->string('user_type');        // stock keeper (admin, manager, warehouse, ...)
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('product_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_stocks');
    }
}
