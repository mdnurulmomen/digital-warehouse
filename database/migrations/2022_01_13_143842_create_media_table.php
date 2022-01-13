<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('media', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 50)->default('media_name');
            $table->string('logo')->default('media_logo.png');
            $table->string('link')->default('media_link.com');
            $table->boolean('publish')->default(true);
            $table->unsignedTinyInteger('application_setting_id')->default(1);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('media');
    }
}
