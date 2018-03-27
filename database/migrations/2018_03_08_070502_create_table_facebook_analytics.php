<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableFacebookAnalytics extends Migration
{
    /**
    * Run the migrations.
    * @param id: increments
    * @param account_id: integer
    * @param account_name: string
    * @param account_created_date: datetime  
    * @param total_posts: integer
    * @param total_followers: integer
    * @param total_likes: integer
    * @param total_likes_posts: integer
    * @param average_posts_per_day: integer
    * @param average_likes_per_day: integer
    * @param best_of_publish_time: time
    * @param date_of_last_post: datetime
     * @return void
     */
    public function up()
    {
        Schema::create('facebook_analytics', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('account_id');
            $table->string('account_name');
            $table->string('account_picture')->nullable();
            $table->string('account_link')->nullable();
            $table->integer('account_type')->nullable()->default(1);
            $table->datetime('account_created_date')->nullable();
            $table->integer('total_page_posts')->nullable()->default(0);
            $table->integer('total_page_followers')->nullable()->default(0);
            $table->integer('total_page_likes')->nullable()->default(0);
            $table->integer('total_posts_likes')->nullable()->default(0);
            $table->integer('total_posts_shares')->nullable()->default(0);
            $table->integer('total_posts_comments')->nullable()->default(0);
            $table->integer('total_posts_hahas')->nullable()->default(0);
            $table->integer('total_posts_wows')->nullable()->default(0);
            $table->integer('total_posts_loves')->nullable()->default(0);
            $table->integer('total_posts_sads')->nullable()->default(0);
            $table->integer('total_posts_thankfuls')->nullable()->default(0);
            $table->integer('total_posts_angries')->nullable()->default(0);
            $table->float('average_posts_per_day')->nullable()->default(0);
            $table->float('average_reactions_per_post')->nullable()->default(0);
            $table->float('average_interactions_per_post')->nullable()->default(0);
            $table->time('best_of_publish_time')->nullable();
            $table->datetime('date_of_last_post')->nullable();
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
        Schema::dropIfExists('facebook_analytics');
    }
}
