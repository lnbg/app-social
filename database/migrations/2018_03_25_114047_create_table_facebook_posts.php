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
            $table->text('messages')->nullable();
            $table->string('picture')->nullable();
            $table->text('story')->nullable();
            $table->integer('reaction_like')->nullable()->default(0);
            $table->integer('reaction_haha')->nullable()->default(0);
            $table->integer('reaction_love')->nullable()->default(0);
            $table->integer('reaction_wow')->nullable()->default(0);
            $table->integer('reaction_sad')->nullable()->default(0);
            $table->integer('reaction_angry')->nullable()->default(0);
            $table->integer('shares')->nullable()->default(0);
            $table->integer('comments')->nullable()->default(0);
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
