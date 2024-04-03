<?php

declare(strict_types=1);

namespace App\Actions\Extract;

use App\Enums\SiteName;
use App\Models\RawPage;
use Illuminate\Support\LazyCollection;

class CursorRawPage
{
    /**
     * @return LazyCollection<int,\App\Models\RawPage>
     */
    public function __invoke(SiteName $siteName): LazyCollection
    {
        return RawPage::where('site_name', $siteName)->cursor();
    }
}
