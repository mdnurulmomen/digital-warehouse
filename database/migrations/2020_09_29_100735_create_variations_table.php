<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVariationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('variations', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);    // small, medium, long in length type,
            $table->unsignedSmallInteger('variation_type_id');      // length, color
            $table->unsignedSmallInteger('variation_parent_id')->nullable();      // length, color
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('variations');
    }
}
