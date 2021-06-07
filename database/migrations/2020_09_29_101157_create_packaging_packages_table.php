<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePackagingPackagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // different packages
        Schema::create('packaging_packages', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100);
            $table->unsignedSmallInteger('price')->default(10);
            $table->string('description')->nullable();
            $table->string('preview')->nullable(); // image of pack / box / rapping-paper
            // $table->unsignedSmallInteger('width')->default(10); // to calculate minimum boxs / units can contain inside this bag / box
            // $table->unsignedSmallInteger('length')->default(10); // to calculate minimum boxs / units can contain inside this bag / box
            // $table->unsignedSmallInteger('quantity_type_id'); // kg / pc / grams / litre / box / bag / packet
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
        Schema::dropIfExists('packaging_packages');
    }
}
