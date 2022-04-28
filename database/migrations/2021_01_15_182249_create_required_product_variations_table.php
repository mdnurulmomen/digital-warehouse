<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredProductVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_product_variations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedMediumInteger('merchant_product_variation_id');
            $table->unsignedMediumInteger('quantity');
            // $table->boolean('has_serials')->default(false);
            $table->string('selected_stock_code')->nullable();
            $table->unsignedInteger('required_product_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_product_variations');
    }
}
