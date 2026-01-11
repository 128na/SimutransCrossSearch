<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE raw_pages MODIFY html LONGBLOB NOT NULL');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE raw_pages MODIFY html LONGTEXT NOT NULL');
    }
};
