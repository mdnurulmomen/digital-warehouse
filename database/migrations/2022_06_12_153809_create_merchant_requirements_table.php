<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMerchantRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('merchant_requirements', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('industry')->nullable();
            $table->unsignedSmallInteger('warehouse_id')->nullable();
            $table->unsignedTinyInteger('container_type_id');
            $table->unsignedTinyInteger('container_id')->nullable();
            $table->unsignedTinyInteger('quantity');
            $table->text('message')->nullable();
            $table->boolean('status')->default(true);   // 1 for not reviewed / 0 for reviewed
            $table->unsignedInteger('merchant_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('merchant_requirements');
    }
}
