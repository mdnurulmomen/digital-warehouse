<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequisitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requisitions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('subject');
            $table->string('description')->nullable();
            // $table->string('product_note')->nullable();
            $table->string('creator_type')->nullable();     // who created the requisition
            $table->unsignedInteger('creator_id')->nullable();
            $table->tinyInteger('status')->default(0);      // pending / dispatched / cancelled
            $table->string('updater_type')->nullable();     // who cancelled / dispatched the requisition
            $table->unsignedInteger('updater_id')->nullable();
            $table->unsignedInteger('merchant_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requisitions');
    }
}
