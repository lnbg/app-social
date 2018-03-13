<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
    * Run the migrations.
    * @param id increments
    * @param social_id: integer
    * @param social_name: string
    * @param type: integer [1: facebook account, 2: twitter account, 3: instagram account]
    * @param access_token: string
    * @param exprired_date: datetime
    * @param timestamps: datetime
    * @return void
    */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('social_id')->unique();
            $table->string('social_name');
            $table->integer('type')->comment('[1: facebook account, 2: twitter account, 3: instagram account]');
            $table->string('access_token');
            $table->datetime('expired_date');
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
        Schema::dropIfExists('users');
    }
}
