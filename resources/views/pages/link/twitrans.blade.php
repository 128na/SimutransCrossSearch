@php
    $url = "{$page->url}?word={$word}";
@endphp
<a target="_blank" rel="noopener noreferrer" class="link-page" href="{{ $url }}">{{ $page->title }}</a>
