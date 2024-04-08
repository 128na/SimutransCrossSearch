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
        Schema::dropIfExists('personal_access_tokens');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('personal_access_tokens', function (Blueprint $blueprint) {
            $blueprint->bigIncrements('id');
            $blueprint->morphs('tokenable');
            $blueprint->string('name');
            $blueprint->string('token')->unique();
            $blueprint->text('abilities');
            $blueprint->timestamp('last_used_at');
            $blueprint->timestamps();
        });
    }
};
