<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApplicationSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('application_settings', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedTinyInteger('vat_percentage')->detault(0);
            $table->unsignedTinyInteger('default_selling_price')->detault(100);
            $table->unsignedTinyInteger('default_storing_price')->detault(100);
            $table->unsignedTinyInteger('default_length')->detault(10);
            $table->unsignedTinyInteger('default_width')->detault(10);
            $table->unsignedTinyInteger('default_height')->detault(10);
            $table->unsignedTinyInteger('default_unit_id')->nullable();
            $table->string('merchant_number', 50)->nullable();
            $table->string('bank_account_number', 50)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('application_settings');
    }
}
