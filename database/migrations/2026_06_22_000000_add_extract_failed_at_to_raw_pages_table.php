<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('raw_pages', function (Blueprint $table): void {
            // 抽出失敗を隔離・可視化する。失敗時に行を削除する代わりにここへ記録する。
            $table->timestamp('extract_failed_at')->nullable()->after('html');
        });
    }

    public function down(): void
    {
        Schema::table('raw_pages', function (Blueprint $table): void {
            $table->dropColumn('extract_failed_at');
        });
    }
};
