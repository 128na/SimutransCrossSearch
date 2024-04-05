<?php

declare(strict_types=1);

namespace App\Actions\Extract\Portal;

use App\Models\Portal\FileInfo;

class FindFileInfo
{
    public function __invoke(int $attachmentId): ?FileInfo
    {
        return FileInfo::query()
            ->where('attachment_id', $attachmentId)
            ->first();
    }
}
