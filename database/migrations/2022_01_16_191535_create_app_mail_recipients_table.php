<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppMailRecipientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('app_mail_recipients', function (Blueprint $table) {
            $table->increments('id');
            $table->string('recipient');  // email of recipient
            $table->boolean('status')->default(false); // 1 for success / 0 for failed
            $table->unsignedInteger('app_mail_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('app_mail_recipients');
    }
}
