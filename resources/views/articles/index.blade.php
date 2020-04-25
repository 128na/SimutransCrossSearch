@extends('layout')

@section('title', 'Top')

@section('content')
    <section class="container mb-5">
        <h2 class="mb-3">Simutransメディア検索</h2>
        @include('articles.search-form')
    </section>
    <section class="container mb-5">
        <h3 class="mb-3">
            最近投稿されたメディア
            <small class="ml-3"><a href="{{ route('articles.search') }}?page=1">更新順一覧</a></small>
        </h3>
        @include('articles.table')
    </section>
@endsection
