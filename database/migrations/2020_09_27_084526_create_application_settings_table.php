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
            $table->unsignedSmallInteger('default_selling_price')->default(100);
            $table->unsignedSmallInteger('default_storing_price')->default(100);
            $table->unsignedSmallInteger('default_length')->default(10);
            $table->unsignedSmallInteger('default_width')->default(10);
            $table->unsignedSmallInteger('default_height')->default(10);
            $table->unsignedTinyInteger('default_measure_unit_id')->nullable();
            $table->string('official_bank_name', 50)->default('XXXX Bank');
            $table->string('official_bank_account_name', 50)->default('Mr/Mrs XXX');
            $table->string('official_bank_account_number', 50)->default('XXX-XXXXX-XXXXX');
            $table->string('official_merchant_name', 50)->default('BKash / Rocket / Nogod');
            $table->string('official_merchant_account_number', 50)->default('XXX-XXXXX-XXXXX');
            $table->string('official_customer_care_number', 50)->default('XXX-XXXXX-XXXXX');
            $table->string('official_mail_address', 50)->default('XXX@email.com');
            $table->string('official_contact_address', 250)->default('XXX-XXXXX-XXXXX');
            $table->unsignedTinyInteger('vat_percentage')->default(0);
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
