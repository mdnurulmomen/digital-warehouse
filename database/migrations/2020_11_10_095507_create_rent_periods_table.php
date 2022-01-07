<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rent_periods', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 100)->unique();
            $table->unsignedSmallInteger('number_days')->nullable()->default(1);
            // $table->string('code', 100);
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
        Schema::dropIfExists('rent_periods');
    }
}
