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
            $table->string('keeper_type');        // stock keeper (admin, manager, warehouse, ...)
            $table->unsignedInteger('keeper_id');
            $table->boolean('has_approval')->default(false);
            $table->string('approver_type')->nullable();        // stock confirmer (admin, manager, warehouse, ...)
            $table->unsignedInteger('approver_id')->nullable();
            $table->unsignedSmallInteger('warehouse_id');
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
