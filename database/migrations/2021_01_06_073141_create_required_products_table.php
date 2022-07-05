<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_products', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('merchant_product_id');
            $table->unsignedMediumInteger('quantity');
            // $table->boolean('has_variations')->default(false);
            // $table->boolean('has_serials')->default(false);
            $table->boolean('packaging_service')->default(false);
            // $table->string('selected_stock_code')->nullable();
            // $table->boolean('confirm_availability')->default(false); // confirm availablity / cancelled
            $table->unsignedTinyInteger('warehouse_id')->nullable(); // which warehouse cancelled / confirmed the required-product-availablity
            $table->unsignedInteger('requisition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_products');
    }
}
