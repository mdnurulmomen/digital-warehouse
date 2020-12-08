<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseStoragePreviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warhouse_storage_previews', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('preview');
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
        Schema::dropIfExists('warhouse_storage_previews');
    }
}
