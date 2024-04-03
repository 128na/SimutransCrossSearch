<div class="row text-center">
    @foreach ($articles as $article)
        <article class="col-sm mb-3">
            <div class="thumbnail-area">
                <a target="_blank" rel="noopener noreferrer" class="link-article" href="{{ $article->url }}">
                    <img src="{{ $article->thumbnail_url }}" class="thumbnail">
                    @includeWhen($article->is_video, 'icons.play')
                </a>
            </div>
            <div>
                <span>{{ $article->display_media_type }}</span>
                <a target="_blank" rel="noopener noreferrer" class="link-article" href="{{ $article->url }}">{{ $article->title }}</a>
            </div>
            <div>
                <small>
                    <a target="_blank" rel="noopener noreferrer" class="link-media" href="{{ $article->site_url }}">{{ $article->display_site_name }}</a>
                    <span>{{ $article->last_modified->format('Y-m-d H:i') }}</span>
                </small>
            </div>
        </article>
    @endforeach
</div>
