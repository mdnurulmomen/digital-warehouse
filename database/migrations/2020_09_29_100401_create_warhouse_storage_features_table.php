<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseStorageFeaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warhouse_storage_features', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('features');
            // $table->unsignedTinyInteger('storage_type_id');
            $table->unsignedMediumInteger('warhouse_storage_type_id');
            // $table->unsignedSmallInteger('warhouse_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warhouse_storage_features');
    }
}
