<div class="items">
    <div class="row p-2 border-bottom">
        <span class="col-6 col-sm-2"><strong>日付</strong></span>
        <span class="col-6 col-sm-2"><strong>Pakサイズ</strong></span>
        <span class="col-6 col-sm-5"><strong>ページ名</strong></span>
        <span class="col-6 col-sm-3"><strong>サイト名</strong></span>
    </div>

    @foreach ($pages as $page)
        <div class="row p-2 border-bottom">
            <span class="col-6 col-sm-2">{{ $page->last_modified->format('Y/m/d') }}</span>
            <span class="col-6 col-sm-2">{{ $page->paks->pluck('name')->implode(', ') }}</span>
            <span class="col-6 col-sm-5"><a target="_blank" rel="noopener noreferrer" class="link-page" href="{{ $page->url }}">{{ $page->title }}</a></span>
            <span class="col-6 col-sm-3"><a target="_blank" rel="noopener noreferrer" class="link-site" href="{{ $page->site_url }}">{{ $page->display_site_name }}</a></span>
        </div>
    @endforeach
</div>
