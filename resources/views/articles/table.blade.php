<ul class="media-tile">
    @foreach ($articles as $article)
    <li>
        <div>
            <a target="_blank" rel="noopener noreferrer" class="link-article" href="{{ $article->url }}">
                <img src="{{ $article->thumbnail_url }}" class="img-thumbnail">
            </a>
        </div>
        <div>
            <span>{{ $article->display_media_type }}</span>
            <span>{{ $article->last_modified->format('Y/m/d H:i:s') }}</span>
            <a target="_blank" rel="noopener noreferrer" class="link-article" href="{{ $article->url }}">{{ $article->title }}</a>
            on <a target="_blank" rel="noopener noreferrer" class="link-site" href="{{ $article->site_url }}">{{ $article->display_site_name }}</a>
        </div>
    </li>
    @endforeach
</ul>
<style>
    .media-tile img{
        max-width:128px;
        max-height:128px;
    }
    </style>
