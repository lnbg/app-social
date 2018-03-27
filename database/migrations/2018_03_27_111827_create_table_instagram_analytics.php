<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInstagramAnalytics extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('instagram_id');
            $table->string('user_name');
            $table->string('name');
            $table->string('picture');
            $table->string('link');
            $table->string('website')->nullable();
            $table->string('biography')->nullable();
            $table->integer('followers_count')->nullable()->default(0);
            $table->integer('follows_count')->nullable()->default(0);
            $table->integer('media_counts')->nullable()->default(0);
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
        Schema::dropIfExists('instagram_analytics');
    }
}
