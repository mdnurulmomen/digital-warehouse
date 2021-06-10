<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequiredProductPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('required_product_packages', function (Blueprint $table) {
            $table->increments('id');
            // $table->unsignedSmallInteger('quantity')->default(10); // quantity of packages needed
            $table->unsignedInteger('required_product_id');
            $table->unsignedSmallInteger('packaging_package_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('required_product_packages');
    }
}
