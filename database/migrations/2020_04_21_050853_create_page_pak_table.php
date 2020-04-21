<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePagePakTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('page_pak', function (Blueprint $table) {
            $table->foreignId('page_id')->constrained()->onDelete('cascade');
            $table->foreignId('pak_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('page_pak', function (Blueprint $table) {
            $table->dropForeign(['page_id']);
            $table->dropForeign(['pak_id']);
        });
        Schema::dropIfExists('page_pak');
    }
}
