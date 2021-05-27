<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionAgentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisition_agents', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->string('mobile', 50);
            $table->string('code', 50);
            $table->unsignedSmallInteger('collection_warehouse_point'); // warehouse_id address
            // $table->boolean('presence_confirmation')->default(false);
            $table->unsignedInteger('requisition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisition_agents');
    }
}
