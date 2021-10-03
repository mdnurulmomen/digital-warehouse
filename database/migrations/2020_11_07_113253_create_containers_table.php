<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContainersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('containers', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100)->unique();
            $table->string('code', 100)->unique();
            $table->unsignedSmallInteger('length')->default(100);
            $table->unsignedSmallInteger('width')->default(100);
            $table->unsignedSmallInteger('height')->default(100);
            // $table->unsignedSmallInteger('storing_price')->default(100);
            // $table->unsignedSmallInteger('selling_price')->default(100);
            $table->boolean('has_shelve')->default(false);
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
        Schema::dropIfExists('containers');
    }
}
