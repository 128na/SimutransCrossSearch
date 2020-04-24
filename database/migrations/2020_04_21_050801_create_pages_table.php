<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('raw_page_id');
            $table->string('site_name');
            $table->string('url', 512)->unique();
            $table->longText('text');
            $table->string('title');
            $table->timestamps();

            $table->foreign('raw_page_id')->references('id')->on('raw_pages')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

        Schema::table('pages', function (Blueprint $table) {
            $table->dropForeign(['raw_page_id']);
        });
        Schema::dropIfExists('pages');
    }
}
