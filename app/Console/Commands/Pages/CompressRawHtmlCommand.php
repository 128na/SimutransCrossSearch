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
                ->chunkById($chunk, function ($chunkRows) use (&$processed, &$skipped, $dryRun, $force) {
                    foreach ($chunkRows as $rp) {
                        $raw = $rp->getAttributes()['html'] ?? '';
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
                            $rp->html = HtmlCompression::decode($raw);
                        } else {
                            $rp->html = $rp->html;
                        }
                        $rp->saveQuietly();
                        $processed++;
                    }
                });

            $logger->info('Finish backfill: compress raw_pages.html', ['processed' => $processed, 'skipped' => $skipped, 'dry' => $dryRun, 'force' => $force]);
            $this->info("processed={$processed} skipped={$skipped} dryRun=" . ($dryRun ? 'yes' : 'no') . ' force=' . ($force ? 'yes' : 'no'));

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            report($throwable);
            $this->error($throwable->getMessage());

            return self::FAILURE;
        }
    }
}
