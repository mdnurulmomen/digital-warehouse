<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductDeliveriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_deliveries', function (Blueprint $table) {
            $table->increments('id');
            // $table->string('destination_name', 100);
            $table->unsignedSmallInteger('delivery_price')->default(100);
            $table->string('receipt_preview'); // receit image of delivery company for delivery responsiblity
            $table->boolean('receiving_confirmation')->default(false);
            $table->unsignedInteger('dispatch_id');
            // $table->unsignedInteger('product_dispatch_id');
            // $table->unsignedSmallInteger('delivery_company_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_deliveries');
    }
}
