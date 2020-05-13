@php
    $parsed = parse_url($page->url);
    $word = urlencode(mb_convert_encoding($word, 'EUC-JP', 'UTF-8'));
    $query = "cmd=read&page={$parsed['query']}&word={$word}";
    $url = "{$parsed['scheme']}://{$parsed['host']}{$parsed['path']}?{$query}";
@endphp
<a target="_blank" rel="noopener noreferrer" class="link-page" href="{{ $url }}">{{ $page->title }}</a>
