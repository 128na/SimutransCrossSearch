<p>
    @foreach (config('media') as $media)
        <a href="{{ $media['url'] }}" class="link-media" target="_blank" rel="noopener noreferrer"><span>{{ $media['display_name'] }}</span></a>
    @endforeach
    に投稿された最近のSimutrans関連動画、画像です。
</p>

<form class="form" action="{{route('articles.index')}}" method="GET">
    <div class="form-group">
        <label>検索条件： </label>
        @php
            $media_types = old('media_types', $media_types ?? []);
        @endphp
        @foreach (config('media_types') as $slug => $media_type)
        <div class="custom-control custom-checkbox custom-control-inline">
            <input type="checkbox" class="custom-control-input"
                id="media_type-{{ $slug }}" name="media_types[]" value={{ $slug }}{{ in_array((string)$slug, $media_types) ? ' checked' : '' }}>
            <label class="custom-control-label" for="media_type-{{ $slug }}" >{{ $media_type }}</label>
        </div>
        @endforeach
        <button type="submit" class="btn btn-outline-primary">検 索</button>
    </div>
</form>
