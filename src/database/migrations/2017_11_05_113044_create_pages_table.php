<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

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
      $table->increments('id');
      $table->string('site_name')->comment('掲載サイト名');
      $table->string('title')->comment('ページ名');
      $table->string('url')->comment('掲載ページURL');
      $table->text('text')->comment('テキスト');
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
    Schema::dropIfExists('pages');
  }
}
