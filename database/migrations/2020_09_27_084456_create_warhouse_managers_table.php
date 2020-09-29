<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhouseManagersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warhouse_managers', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('warhouse_id');
            $table->unsignedSmallInteger('manager_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('warhouse_managers');
    }
}
