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
            $table->string('receiver_type'); // product-delivery or product returns
            $table->unsignedMediumInteger('receiver_id'); // for details of taker
            $table->unsignedInteger('merchant_id');
            $table->timestamp('released_at')->useCurrent();
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
