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
            $table->unsignedTinyInteger('company')->nullable();
            $table->string('required_size');
            $table->unsignedSmallInteger('merchant_id');
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
