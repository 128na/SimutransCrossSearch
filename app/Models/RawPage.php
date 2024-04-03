<?php

namespace App\Models;

use App\Enums\SiteName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RawPage extends Model
{
    use HasFactory;

    protected $fillable = [
        'site_name',
        'url',
        'html',
    ];

    protected $casts = [
        'site_name' => SiteName::class,
    ];

    /**
     * @return HasOne<Page>
     */
    public function page(): HasOne
    {
        return $this->hasOne(Page::class);
    }
}
