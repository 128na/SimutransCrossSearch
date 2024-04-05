<?php

declare(strict_types=1);

namespace App\Models\Portal;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $attachment_id
 * @property string $data
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 *
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|FileInfo query()
 *
 * @mixin \Eloquent
 */
class FileInfo extends Model
{
    use HasFactory;

    protected $connection = 'portal';

    protected $fillable = [
        'data',
    ];
}
