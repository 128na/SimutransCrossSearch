<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLastModifiedColumnInPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->timestamp('last_modified')->default('1970-01-01 00:00:00')->after('title')->comment('元記事の最終更新日時');
            $table->index(['last_modified']);
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
            $table->dropIndex(['last_modified']);
            $table->dropColumn('last_modified');
        });
    }
}
