<table class="table">
    <thead>
        <th>更新日</th>
        <th>Pak</th>
        <th>ページ</th>
        <th>サイト</th>
    </thead>
    <tbody>
        @foreach ($pages as $page)
        <tr>
            <td>
                {{ $page->last_modified->format('Y/m/d') }}
            </td>
            <td>
                {{ $page->paks->pluck('name')->implode(', ') }}
            </td>
            <td>
                <a target="_blank" rel="noopener noreferrer" class="link-page" href="{{ $page->url }}"><span>{{ $page->title }}</span></a>
            </td>
            <td>
                <a target="_blank" rel="noopener noreferrer" class="link-site" href="{{ $page->site_url }}"><span>{{ $page->display_site_name }}</span></a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
