<?php

declare(strict_types=1);

namespace App\Casts;

use App\Support\HtmlCompression;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/**
 * @implements CastsAttributes<string, string>
 */
final class CompressedHtml implements CastsAttributes
{
    /**
     * Decompress the stored gzip data back to plain HTML.
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): string
    {
        if (! is_string($value)) {
            return '';
        }

        return HtmlCompression::decode($value);
    }

    /**
     * Compress plain HTML using gzip for storage efficiency.
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string
    {
        $input = is_string($value) ? $value : '';

        return HtmlCompression::encode($input);
    }
}
