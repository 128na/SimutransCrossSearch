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
        Schema::dropIfExists('search_logs');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('search_logs', function (Blueprint $blueprint) {
            $blueprint->bigIncrements('id');
            $blueprint->string('query');
            $blueprint->unsignedInteger('count');
            $blueprint->timestamps();
        });
    }
};
