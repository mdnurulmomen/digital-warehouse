<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDispatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // history of all dispatches
        Schema::create('dispatches', function (Blueprint $table) {
            $table->increments('id');
            // $table->timestamp('released_at')->useCurrent();
            $table->tinyInteger('has_approval')->default(0);      // pending / approved / cancelled dispatch
            $table->string('updater_type')->nullable();     // who cancelled / approved the dispatch
            $table->unsignedInteger('updater_id')->nullable();
            $table->timestamp('updated_at')->nullable();
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
        Schema::dropIfExists('dispatches');
    }
}
