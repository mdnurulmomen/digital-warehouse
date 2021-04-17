<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateModelHasRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('model_has_roles', function (Blueprint $table) {
            $table->unsignedMediumInteger('role_id');
            $table->unsignedInteger('model_id');
            $table->string('model_type');

            //FOREIGN KEY CONSTRAINTS
            $table->foreign('role_id')->references('id')->on('roles')->onDelete('cascade');
 
            //SETTING THE PRIMARY KEYS
            $table->primary(['model_id','model_type','role_id']); // composite key
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('model_has_roles');
    }
}
