<p>
    @foreach (config('sites') as $site)
        <a href="{{ $site['url'] }}" class="link-site" target="_blank" rel="noopener noreferrer">
            <span>{{ $site['display_name'] }}</span></a>
    @endforeach
    に投稿されているアドオンをまとめて検索できます。
</p>
<form class="form" action="{{route('pages.search')}}" method="GET">
    <div class="form-group">
        <div class="input-group">
            <input class="form-control flex-2" name="word" type="search" placeholder="キーワード" aria-label="Search" value="{{ old('word', $word ?? '') }}">
            @php
                $type = old('type', $type ?? 'and');
            @endphp
            <select class="form-control" name="type">
                <option value="and" {{ $type === 'and' ? 'selected' : '' }}>全てを含む</option>
                <option value="or" {{ $type === 'or' ? 'selected' : '' }}>いずれかを含む</option>
            </select>
            <div class="input-group-append">
                <button class="btn btn-outline-primary" type="submit">検 索</button>
            </div>
        </div>
        <small>
            ※ 半角、全角スペースで区切って複数キーワード検索が可能です。<br>
        </small>
    </div>
    <div class="form-group">
        @php
            $paks = old('paks', $paks ?? []);
        @endphp
        @foreach (config('paks') as $slug => $pak)
        <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input"
                id="pak-{{ $slug }}" name="paks[]" value={{ $slug }}{{ in_array((string)$slug, $paks) ? ' checked' : '' }}>
            <label class="custom-control-label" for="pak-{{ $slug }}" >{{ $pak }}</label>
        </div>
    @endforeach
    </div>
</form>
