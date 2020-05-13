<div class="items">
    @foreach ($pages as $page)
        <div class="p-2 border-bottom">
            <div>
                <span class="badge badge-primary mb-2">{{ $page->paks->pluck('name')->implode(', ') }}</span>
                <span class=" mb-2">
                    @includeIf("pages.link.{$page->site_name}")
                </span>
            </div>
            @isset($search_condition)
                <div class=" mb-2">
                    <small>{!! $page->highlightText($search_condition) !!}</small>
                </div>
            @endisset
            <div>
                <span class=" mb-2">[{{ $page->last_modified->format('Y/m/d') }}]</span>
                <span class=" mb-2"><a target="_blank" rel="noopener noreferrer" class="link-site" href="{{ $page->site_url }}">{{ $page->display_site_name }}</a></span>
            </div>
        </div>
    @endforeach
</div>
