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
            $table->string('stock_code');
            $table->unsignedMediumInteger('stock_quantity');    // quantity including all variations
            $table->unsignedMediumInteger('available_quantity');
            $table->unsignedDecimal('unit_buying_price', $precision = 8, $scale = 2)->default(0);
            // $table->boolean('has_variations')->default(false);
            // $table->boolean('has_serials')->default(false);
            $table->date('manufactured_at')->nullable();
            $table->date('expired_at')->nullable();
            $table->unsignedMediumInteger('vendor_id')->nullable();
            $table->unsignedMediumInteger('location_id')->nullable();
            $table->unsignedInteger('merchant_product_id');
            $table->unsignedInteger('stock_id');
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
        Schema::dropIfExists('product_stocks');
    }
}
