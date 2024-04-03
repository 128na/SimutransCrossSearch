@php
    $url = isset($word) ? "{$page->url}?word={$word}" : $page->url;
@endphp
<a target="_blank" rel="noopener noreferrer" class="link-page" href="{{ $url }}">{{ $page->title }}</a>
