<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('articles');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('articles', function (Blueprint $blueprint) {
            $blueprint->bigIncrements('id');
            $blueprint->string('site_name');
            $blueprint->string('media_type');
            $blueprint->string('title');
            $blueprint->string('url')->unique();
            $blueprint->text('text');
            $blueprint->string('thumbnail_url');
            $blueprint->timestamp('last_modified')->index();
            $blueprint->timestamps();
        });
    }
};
