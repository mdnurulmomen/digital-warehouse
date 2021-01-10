<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarehouseStoragePreviewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warehouse_storage_previews', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->string('preview');
            // $table->unsignedTinyInteger('storage_type_id');
            $table->unsignedMediumInteger('warehouse_storage_type_id');
            // $table->unsignedSmallInteger('warehouse_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warehouse_storage_previews');
    }
}
