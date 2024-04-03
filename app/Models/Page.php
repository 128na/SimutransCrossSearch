<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\SiteName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Page extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'url',
        'title',
        'text',
        'last_modified',
    ];

    protected $casts = [
        'site_name' => SiteName::class,
        'last_modified' => 'datetime',
    ];

    /**
     * @return BelongsToMany<Pak>
     */
    public function paks(): BelongsToMany
    {
        return $this->belongsToMany(Pak::class);
    }

    /**
     * @return BelongsTo<RawPage,Page>
     */
    public function rawPage(): BelongsTo
    {
        return $this->belongsTo(RawPage::class);
    }
}
