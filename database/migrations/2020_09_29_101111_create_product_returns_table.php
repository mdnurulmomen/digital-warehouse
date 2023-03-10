<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductReturnsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_returns', function (Blueprint $table) {
            $table->increments('id');
            // $table->unsignedInteger('product_dispatch_id');
            // $table->string('receipt_preview');
            // $table->unsignedSmallInteger('collection_point'); // warehouse_id address
            $table->string('collection_point'); // warehouse_id address
            $table->boolean('receiving_confirmation')->default(false);
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
        Schema::dropIfExists('product_returns');
    }
}
