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
            // quantity including all variations
            $table->mediumInteger('stock_quantity')->default(0); 
            $table->mediumInteger('available_quantity')->default(0);
            // $table->unsignedMediumInteger('total_quantity')->default(0);
            $table->unsignedInteger('product_id');
            $table->string('user_type');
            $table->string('user_id');
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
