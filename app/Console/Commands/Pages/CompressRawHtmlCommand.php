<?php

declare(strict_types=1);

namespace App\Console\Commands\Pages;

use App\Models\RawPage;
use App\Support\HtmlCompression;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

final class CompressRawHtmlCommand extends Command
{
    /**
     * @var string
     */
    protected $signature = 'app:compress-raw-html {--chunk=500 : Process N records per chunk} {--dry-run : Only count candidates, do not write} {--force : Recompress already gzipped data}';

    /**
     * @var string
     */
    protected $description = 'raw_pages.html をgzip圧縮へ移行（既存データを一括処理）';

    public function handle(): int
    {
        try {
            $chunk = (int) $this->option('chunk');
            $dryRun = (bool) $this->option('dry-run');
            $force = (bool) $this->option('force');

            $logger = Log::stack(['daily', 'stdout']);
            $logger->info('Start backfill: compress raw_pages.html', ['chunk' => $chunk, 'dry' => $dryRun, 'force' => $force]);

            $processed = 0;
            $skipped = 0;

            RawPage::query()
                ->select(['id', 'html'])
                ->orderBy('id')
                ->chunkById($chunk, function ($chunkRows) use (&$processed, &$skipped, $dryRun, $force): void {
                    foreach ($chunkRows as $chunkRow) {
                        $raw = $chunkRow->getAttributes()['html'] ?? '';
                        // Skip already gzip data unless --force is specified
                        if (! $force && is_string($raw) && HtmlCompression::isGzip($raw)) {
                            $skipped++;

                            continue;
                        }

                        if ($dryRun) {
                            $processed++;

                            continue;
                        }

                        // Reassign to trigger cast compression
                        // For already compressed data with --force, decode first then re-encode
                        if ($force && is_string($raw) && HtmlCompression::isGzip($raw)) {
                            $chunkRow->html = HtmlCompression::decode($raw);
                        } else {
                        }

                        $chunkRow->saveQuietly();
                        $processed++;
                    }
                });

            $logger->info('Finish backfill: compress raw_pages.html', ['processed' => $processed, 'skipped' => $skipped, 'dry' => $dryRun, 'force' => $force]);
            $this->info(sprintf('processed=%d skipped=%d dryRun=', $processed, $skipped).($dryRun ? 'yes' : 'no').' force='.($force ? 'yes' : 'no'));

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
