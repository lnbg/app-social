<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableInstagramMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('instagram_media', function (Blueprint $table) {
            $table->bigInteger('media_id');
            $table->integer('instagram_analytics_id');
            $table->string('media_type')->nullable();
            $table->text('caption')->nullable();
            $table->string('url')->nullable();
            $table->string('dimension')->nullable();
            $table->integer('likes')->nullable()->default(0);
            $table->integer('comments')->nullable()->default(0);
            $table->string('location')->nullable();
            $table->text('tags')->nullable();
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
        Schema::dropIfExists('instagram_media');
    }
}
