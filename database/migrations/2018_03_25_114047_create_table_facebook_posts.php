<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFacebookPosts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_posts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('facebook_analytics_id');
            $table->string('facebook_post_id');
            $table->text('messages');
            $table->text('story');
            $table->integer('reaction_like');
            $table->integer('reaction_haha');
            $table->integer('reaction_love');
            $table->integer('reaction_wow');
            $table->integer('reaction_sad');
            $table->integer('reaction_angry');
            $table->integer('shares');
            $table->integer('comments');
            $table->datetime('facebook_created_at');
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
        Schema::dropIfExists('facebook_posts');
    }
}
