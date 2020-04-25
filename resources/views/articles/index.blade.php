@extends('layout')

@section('title', 'Top')

@section('content')
    <section class="container mb-5">
        <h2 class="mb-3">
            最近投稿されたメディア
        </h2>
        <p>
            @foreach (config('media') as $media)
                <a href="{{ $media['url'] }}" class="link-media" target="_blank" rel="noopener noreferrer"><span>{{ $media['display_name'] }}</span></a>
            @endforeach
            に投稿された最近3か月ぐらいのSimutrans関連の動画、画像です。
        </p>
        @include('articles.table')
    </section>
@endsection
