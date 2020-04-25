<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('site_name');
            $table->string('media_type')->comment('記事形式 video:動画,image:画像,other:一般記事');
            $table->string('title');
            $table->string('url', 512)->unique();
            $table->longText('text');
            $table->string('thumbnail_url', 512)->nullable();
            $table->timestamp('last_modified')->nullable()->comment('元記事の最終更新日時');
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
        Schema::dropIfExists('articles');
    }
}
