<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWarhousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('warhouses', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('name', 100)->nullable();
            // (auto-generated if no input)
            $table->string('code', 100)->unique(); 
            $table->string('user_name', 100)->unique();
            $table->string('email', 100)->unique();
            $table->string('mobile', 50)->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            // graph image, may be added at profile_previews table
            $table->string('site_map_preview', 100)->nullable();
            // may be a separate table for location, lat, lng.
            // may be a separate table for location, lat, lng
            $table->string('lat', 100);
            $table->string('lng', 100);
            // descripton of warhouse deals (ex. rental-2000, contractual till december 2020)
            $table->string('warhouse_deal')->default('No Deal');
            $table->boolean('active')->default(0);
            $table->unsignedSmallInteger('warhouse_owner_id');

            $table->rememberToken();

            $table->softDeletes();
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
        Schema::dropIfExists('warhouses');
    }
}
