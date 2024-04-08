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
        Schema::dropIfExists('sessions');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('sessions', function (Blueprint $blueprint) {
            $blueprint->string('id')->primary();
            $blueprint->foreignId('user_id');
            $blueprint->string('ip_address');
            $blueprint->text('user_agent');
            $blueprint->text('payload');
            $blueprint->integer('last_activity');
        });
    }
};
