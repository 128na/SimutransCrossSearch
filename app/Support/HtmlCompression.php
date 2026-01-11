<?php

declare(strict_types=1);

namespace App\Support;

final class HtmlCompression
{
    private const GZIP_MAGIC = "\x1f\x8b";

    public static function isGzip(string $data): bool
    {
        return $data !== '' && str_starts_with($data, self::GZIP_MAGIC);
    }

    /**
     * @return string Compressed data when possible; falls back to original on failure
     */
    public static function encode(string $plain, int $level = 6): string
    {
        if ($plain === '') {
            return '';
        }

        // Avoid double-compressing
        if (self::isGzip($plain)) {
            return $plain;
        }

        $compressed = gzencode($plain, $level);
        return $compressed !== false ? $compressed : $plain;
    }

    /**
     * @return string Plain data when compressed; original value when not gzip or decode fails
     */
    public static function decode(string $maybeCompressed): string
    {
        if ($maybeCompressed === '') {
            return '';
        }

        if (self::isGzip($maybeCompressed)) {
            $decoded = gzdecode($maybeCompressed);
            if ($decoded !== false) {
                return $decoded;
            }
        }

        return $maybeCompressed;
    }
}
