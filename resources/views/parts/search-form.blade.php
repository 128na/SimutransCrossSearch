<p>
    @foreach (config('sites') as $site)
        <a target="_blank" rel="noopener noreferrer" href="{{ $site['url'] }}">
            <span>{{ $site['display_name'] }}</span></a>
    @endforeach
    に投稿されているアドオンをまとめて検索できます。
</p>
<form class="form" action="{{route('search')}}" method="GET">
    <div class="form-group">
        <div class="input-group">
            <input class="form-control" name="word" type="search" placeholder="キーワード" aria-label="Search" value="{{ $word ?? '' }}">
            <div class="input-group-append">
                <button class="btn btn-outline-primary my-2 my-sm-0" type="submit">検索</button>
            </div>
        </div>
    </div>
    <div class="form-group">
        @foreach (config('paks') as $slug => $pak)
        <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input"
                id="pak-{{ $slug }}" name="paks[]" value={{ $slug }}{{ in_array((string)$slug, $paks ?? []) ? ' checked' : '' }}>
            <label class="custom-control-label" for="pak-{{ $slug }}" >{{ $pak }}</label>
        </div>
    @endforeach
    </div>
</form>
<small>
    ※ &amp;, スペースで and, or 検索が可能です。<br>
    （JR 国鉄&103系 → （JRまたは国鉄）かつ103系）
</small>
